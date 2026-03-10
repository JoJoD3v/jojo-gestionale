<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Lavoro;
use App\Models\Pagamento;
use App\Models\PagamentoRicorrenza;
use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ChatbotService
{
    private string $apiKey;
    private string $model;
    private string $apiUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key', '');
        $this->model  = config('services.openai.model', 'gpt-4o-mini');
    }

    /**
     * Raccoglie i dati del gestionale in sola lettura e li formatta
     * come testo per il system prompt.
     */
    private function buildContext(): string
    {
        $oggi = Carbon::today();
        $meseCorrente = Carbon::now()->startOfMonth();
        $fineCorrente = Carbon::now()->endOfMonth();

        // --- Tasks ---
        $taskInSospeso = Task::with('lavoro.cliente')
            ->where('status', 'in_sospeso')
            ->whereNull('deleted_at')
            ->orderBy('scadenza')
            ->get();

        $taskOggi = $taskInSospeso->filter(fn($t) => $t->scadenza && $t->scadenza->isToday());
        $taskInRitardo = $taskInSospeso->filter(fn($t) => $t->scadenza && $t->scadenza->lt($oggi));
        $taskFuturi = $taskInSospeso->filter(fn($t) => $t->scadenza && $t->scadenza->gt($oggi));

        // --- Lavori ---
        $lavoriAttivi = Lavoro::with('cliente')
            ->whereIn('stato', ['da_fare', 'in_corso'])
            ->whereNull('deleted_at')
            ->orderBy('data_lavoro')
            ->get();

        // --- Solo pagamenti oneshot in sospeso (i periodici sono gestiti tramite ricorrenze) ---
        $pagamentiInSospeso = Pagamento::with('cliente')
            ->where('stato', 'in_sospeso')
            ->where('cadenza', 'oneshot')
            ->orderBy('data_scadenza')
            ->get();

        $pagamentiScaduti    = $pagamentiInSospeso->filter(fn($p) => $p->data_scadenza && $p->data_scadenza->lt($meseCorrente));
        $pagamentiQuestoMese = $pagamentiInSospeso->filter(fn($p) => $p->data_scadenza && $p->data_scadenza->between($meseCorrente, $fineCorrente));
        $pagamentiFuturi     = $pagamentiInSospeso->filter(fn($p) => $p->data_scadenza && $p->data_scadenza->gt($fineCorrente));

        // --- Ricorrenze periodiche in sospeso (qualunque data) ---
        $ricorrenzeInSospeso = PagamentoRicorrenza::with('pagamento.cliente')
            ->where('stato', 'in_sospeso')
            ->orderBy('data_ricorrenza')
            ->get();

        $ricorrenzeScadute    = $ricorrenzeInSospeso->filter(fn($r) => $r->data_ricorrenza && Carbon::parse($r->data_ricorrenza)->lt($meseCorrente));
        $ricorrenzeQuestoMese = $ricorrenzeInSospeso->filter(fn($r) => $r->data_ricorrenza && Carbon::parse($r->data_ricorrenza)->between($meseCorrente, $fineCorrente));
        $ricorrenzeFuture     = $ricorrenzeInSospeso->filter(fn($r) => $r->data_ricorrenza && Carbon::parse($r->data_ricorrenza)->gt($fineCorrente));

        // --- Tutti i clienti con dati di contatto ---
        $clienti = Cliente::orderBy('nome')->get();

        // --- Totali rapidi ---
        $totaleClienti = $clienti->count();
        $totaleTaskCompletati = Task::where('status', 'completato')->whereNull('deleted_at')->count();

        // Costruzione del testo di contesto
        $lines = [];
        $lines[] = "DATA ODIERNA: " . $oggi->format('d/m/Y');
        $lines[] = "MESE CORRENTE: " . $meseCorrente->format('F Y');
        $lines[] = "";

        $lines[] = "=== STATISTICHE GENERALI ===";
        $lines[] = "- Clienti totali: {$totaleClienti}";
        $lines[] = "- Task completati totali: {$totaleTaskCompletati}";
        $totaleInSospeso = $pagamentiInSospeso->sum('importo')
            + $ricorrenzeInSospeso->sum(fn($r) => $r->pagamento?->importo ?? 0);
        $lines[] = "- Totale da incassare (tutti i periodi): €" . number_format((float)$totaleInSospeso, 2, ',', '.');
        $lines[] = "";

        // Tasks oggi
        $lines[] = "=== TASK DA COMPLETARE OGGI (" . $taskOggi->count() . ") ===";
        if ($taskOggi->isEmpty()) {
            $lines[] = "Nessun task in scadenza oggi.";
        } else {
            foreach ($taskOggi as $t) {
                $lavoro = $t->lavoro ? $t->lavoro->descrizione : 'N/D';
                $cliente = $t->lavoro && $t->lavoro->cliente ? $t->lavoro->cliente->nome : 'N/D';
                $lines[] = "- \"{$t->nome}\" (Lavoro: {$lavoro} | Cliente: {$cliente})";
            }
        }
        $lines[] = "";

        // Tasks in ritardo
        $lines[] = "=== TASK IN RITARDO (" . $taskInRitardo->count() . ") ===";
        if ($taskInRitardo->isEmpty()) {
            $lines[] = "Nessun task in ritardo.";
        } else {
            foreach ($taskInRitardo as $t) {
                $scadenza = $t->scadenza ? $t->scadenza->format('d/m/Y') : 'N/D';
                $giorni = $t->scadenza ? $t->scadenza->diffInDays($oggi) : '?';
                $lavoro = $t->lavoro ? $t->lavoro->descrizione : 'N/D';
                $lines[] = "- \"{$t->nome}\" scaduto il {$scadenza} ({$giorni} giorni fa) | Lavoro: {$lavoro}";
            }
        }
        $lines[] = "";

        // Task futuri in sospeso
        $lines[] = "=== TASK FUTURI IN SOSPESO (" . $taskFuturi->count() . ") ===";
        if ($taskFuturi->isEmpty()) {
            $lines[] = "Nessun task futuro in sospeso.";
        } else {
            foreach ($taskFuturi->take(20) as $t) {
                $scadenza = $t->scadenza ? $t->scadenza->format('d/m/Y') : 'N/D';
                $lavoro = $t->lavoro ? $t->lavoro->descrizione : 'N/D';
                $lines[] = "- \"{$t->nome}\" entro il {$scadenza} | Lavoro: {$lavoro}";
            }
        }
        $lines[] = "";

        // Lavori attivi
        $lines[] = "=== LAVORI ATTIVI (" . $lavoriAttivi->count() . ") ===";
        if ($lavoriAttivi->isEmpty()) {
            $lines[] = "Nessun lavoro attivo.";
        } else {
            foreach ($lavoriAttivi as $l) {
                $data = $l->data_lavoro ? $l->data_lavoro->format('d/m/Y') : 'N/D';
                $cliente = $l->cliente ? $l->cliente->nome : 'N/D';
                $stato = $l->stato;
                $lines[] = "- \"{$l->descrizione}\" | Cliente: {$cliente} | Data: {$data} | Stato: {$stato}";
            }
        }
        $lines[] = "";

        // Pagamenti scaduti (mesi precedenti)
        $totScaduti = $pagamentiScaduti->sum('importo');
        $lines[] = "=== PAGAMENTI SCADUTI NON PAGATI (" . $pagamentiScaduti->count() . " | €" . number_format((float)$totScaduti, 2, ',', '.') . ") ===";
        if ($pagamentiScaduti->isEmpty()) {
            $lines[] = "Nessun pagamento scaduto.";
        } else {
            foreach ($pagamentiScaduti as $p) {
                $scadenza = $p->data_scadenza ? $p->data_scadenza->format('d/m/Y') : 'N/D';
                $cliente  = $p->cliente ? $p->cliente->nome : 'N/D';
                $importo  = number_format((float)$p->importo, 2, ',', '.');
                $cadenza  = $p->cadenza === 'periodico' ? "periodico/{$p->frequenza}" : 'oneshot';
                $lines[]  = "- €{$importo} da {$cliente} per \"{$p->tipo_lavoro}\" [{$cadenza}] | Scaduto il {$scadenza}";
            }
        }
        $lines[] = "";

        // Pagamenti questo mese
        $totQuestoMese = $pagamentiQuestoMese->sum('importo');
        $lines[] = "=== PAGAMENTI IN SOSPESO QUESTO MESE (" . $pagamentiQuestoMese->count() . " | €" . number_format((float)$totQuestoMese, 2, ',', '.') . ") ===";
        if ($pagamentiQuestoMese->isEmpty()) {
            $lines[] = "Nessun pagamento in sospeso questo mese.";
        } else {
            foreach ($pagamentiQuestoMese as $p) {
                $scadenza = $p->data_scadenza ? $p->data_scadenza->format('d/m/Y') : 'N/D';
                $cliente  = $p->cliente ? $p->cliente->nome : 'N/D';
                $importo  = number_format((float)$p->importo, 2, ',', '.');
                $cadenza  = $p->cadenza === 'periodico' ? "periodico/{$p->frequenza}" : 'oneshot';
                $lines[]  = "- €{$importo} da {$cliente} per \"{$p->tipo_lavoro}\" [{$cadenza}] | Scadenza: {$scadenza}";
            }
        }
        $lines[] = "";

        // Pagamenti futuri
        $totFuturi = $pagamentiFuturi->sum('importo');
        $lines[] = "=== PAGAMENTI IN SOSPESO PROSSIMI MESI (" . $pagamentiFuturi->count() . " | €" . number_format((float)$totFuturi, 2, ',', '.') . ") ===";
        if ($pagamentiFuturi->isEmpty()) {
            $lines[] = "Nessun pagamento futuro in sospeso.";
        } else {
            foreach ($pagamentiFuturi->take(15) as $p) {
                $scadenza = $p->data_scadenza ? $p->data_scadenza->format('d/m/Y') : 'N/D';
                $cliente  = $p->cliente ? $p->cliente->nome : 'N/D';
                $importo  = number_format((float)$p->importo, 2, ',', '.');
                $cadenza  = $p->cadenza === 'periodico' ? "periodico/{$p->frequenza}" : 'oneshot';
                $lines[]  = "- €{$importo} da {$cliente} per \"{$p->tipo_lavoro}\" [{$cadenza}] | Scadenza: {$scadenza}";
            }
        }
        $lines[] = "";

        // Ricorrenze periodiche scadute
        $totRicScadute = $ricorrenzeScadute->sum(fn($r) => $r->pagamento ? $r->pagamento->importo : 0);
        $lines[] = "=== RICORRENZE PERIODICHE SCADUTE NON PAGATE (" . $ricorrenzeScadute->count() . " | €" . number_format((float)$totRicScadute, 2, ',', '.') . ") ===";
        if ($ricorrenzeScadute->isEmpty()) {
            $lines[] = "Nessuna ricorrenza periodica scaduta.";
        } else {
            foreach ($ricorrenzeScadute as $r) {
                $data    = $r->data_ricorrenza ? Carbon::parse($r->data_ricorrenza)->format('d/m/Y') : 'N/D';
                $cliente = $r->pagamento && $r->pagamento->cliente ? $r->pagamento->cliente->nome : 'N/D';
                $tipo    = $r->pagamento ? $r->pagamento->tipo_lavoro : 'N/D';
                $importo = $r->pagamento ? number_format((float)$r->pagamento->importo, 2, ',', '.') : 'N/D';
                $lines[] = "- €{$importo} da {$cliente} per \"{$tipo}\" | Scaduta il {$data}";
            }
        }
        $lines[] = "";

        // Ricorrenze periodiche questo mese
        $totRicMese = $ricorrenzeQuestoMese->sum(fn($r) => $r->pagamento ? $r->pagamento->importo : 0);
        $lines[] = "=== RICORRENZE PERIODICHE IN SOSPESO QUESTO MESE (" . $ricorrenzeQuestoMese->count() . " | €" . number_format((float)$totRicMese, 2, ',', '.') . ") ===";
        if ($ricorrenzeQuestoMese->isEmpty()) {
            $lines[] = "Nessuna ricorrenza periodica in sospeso questo mese.";
        } else {
            foreach ($ricorrenzeQuestoMese as $r) {
                $data    = $r->data_ricorrenza ? Carbon::parse($r->data_ricorrenza)->format('d/m/Y') : 'N/D';
                $cliente = $r->pagamento && $r->pagamento->cliente ? $r->pagamento->cliente->nome : 'N/D';
                $tipo    = $r->pagamento ? $r->pagamento->tipo_lavoro : 'N/D';
                $importo = $r->pagamento ? number_format((float)$r->pagamento->importo, 2, ',', '.') : 'N/D';
                $lines[] = "- €{$importo} da {$cliente} per \"{$tipo}\" | Data ricorrenza: {$data}";
            }
        }
        $lines[] = "";

        // Ricorrenze periodiche future (max 15)
        $totRicFuture = $ricorrenzeFuture->sum(fn($r) => $r->pagamento ? $r->pagamento->importo : 0);
        $lines[] = "=== RICORRENZE PERIODICHE IN SOSPESO PROSSIMI MESI (" . $ricorrenzeFuture->count() . " | €" . number_format((float)$totRicFuture, 2, ',', '.') . ") ===";
        if ($ricorrenzeFuture->isEmpty()) {
            $lines[] = "Nessuna ricorrenza periodica futura in sospeso.";
        } else {
            foreach ($ricorrenzeFuture->take(15) as $r) {
                $data    = $r->data_ricorrenza ? Carbon::parse($r->data_ricorrenza)->format('d/m/Y') : 'N/D';
                $cliente = $r->pagamento && $r->pagamento->cliente ? $r->pagamento->cliente->nome : 'N/D';
                $tipo    = $r->pagamento ? $r->pagamento->tipo_lavoro : 'N/D';
                $importo = $r->pagamento ? number_format((float)$r->pagamento->importo, 2, ',', '.') : 'N/D';
                $lines[] = "- €{$importo} da {$cliente} per \"{$tipo}\" | Data ricorrenza: {$data}";
            }
        }
        $lines[] = "";

        // --- Clienti ---
        $lines[] = "=== RUBRICA CLIENTI (" . $clienti->count() . ") ===";
        if ($clienti->isEmpty()) {
            $lines[] = "Nessun cliente presente.";
        } else {
            foreach ($clienti as $c) {
                $telefono   = $c->telefono    ?: 'non specificato';
                $email      = $c->email       ?: 'non specificata';
                $piva       = $c->partita_iva ?: 'non specificata';
                $note       = $c->note        ? " | Note: {$c->note}" : '';
                $lines[] = "- {$c->nome} | Tel: {$telefono} | Email: {$email} | P.IVA/CF: {$piva}{$note}";
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Invia una domanda a ChatGPT con il contesto del gestionale.
     * Restituisce la risposta testuale.
     *
     * @param  string  $domanda
     * @param  array   $cronologia  Array di {role, content} dei messaggi precedenti
     * @return string
     */
    public function ask(string $domanda, array $cronologia = []): string
    {
        if (empty($this->apiKey)) {
            return 'Errore: OPENAI_API_KEY non configurata. Aggiungi la chiave nel file .env.';
        }

        $contesto = $this->buildContext();

        $systemPrompt = <<<EOT
Sei un assistente virtuale integrato nel gestionale freelance di questo sviluppatore.
Hai accesso in SOLA LETTURA ai dati del gestionale: task, lavori, pagamenti, clienti.
Rispondi SOLO in italiano, in modo conciso e diretto.
Usa i dati qui sotto per rispondere alle domande. Se non trovi un'informazione nei dati, dillo chiaramente.
NON inventare dati, NON fare ipotesi su informazioni non presenti nel contesto.

Puoi usare liste puntate o grassetto Markdown per rendere le risposte più leggibili.

--- DATI ATTUALI DEL GESTIONALE ---
{$contesto}
--- FINE DATI ---
EOT;

        $messaggi = [['role' => 'system', 'content' => $systemPrompt]];

        // Aggiungi la cronologia (ultimi N scambi)
        foreach ($cronologia as $msg) {
            if (isset($msg['role'], $msg['content'])) {
                $messaggi[] = [
                    'role'    => in_array($msg['role'], ['user', 'assistant']) ? $msg['role'] : 'user',
                    'content' => mb_substr((string) $msg['content'], 0, 2000),
                ];
            }
        }

        $messaggi[] = ['role' => 'user', 'content' => $domanda];

        $response = Http::withToken($this->apiKey)
            ->timeout(30)
            ->post($this->apiUrl, [
                'model'       => $this->model,
                'messages'    => $messaggi,
                'max_tokens'  => 1500,
                'temperature' => 0.4,
            ]);

        if ($response->failed()) {
            $error = $response->json('error.message', 'Errore sconosciuto dall\'API OpenAI.');
            return "Errore API: {$error}";
        }

        return $response->json('choices.0.message.content', 'Nessuna risposta ricevuta dall\'API.');
    }
}
