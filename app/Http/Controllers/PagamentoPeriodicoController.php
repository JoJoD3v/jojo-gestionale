<?php

namespace App\Http\Controllers;

use App\Models\Pagamento;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PagamentoPeriodicoController extends Controller
{
    /**
     * Display pagamenti periodici con navigazione mensile
     */
    public function index(Request $request)
    {
        // Ottieni il mese da visualizzare (default: mese corrente)
        $mese = $request->get('mese', now()->format('Y-m'));
        $dataInizio = Carbon::parse($mese . '-01')->startOfMonth();
        $dataFine = $dataInizio->copy()->endOfMonth();

        // Prendi TUTTI i pagamenti periodici (filtreremo dopo)
        $query = Pagamento::with('cliente')
            ->where('cadenza', 'periodico');

        // Filtro per stato
        if ($request->filled('stato')) {
            $query->where('stato', $request->stato);
        }

        // Filtro per cliente
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        // Filtro per frequenza
        if ($request->filled('frequenza')) {
            $query->where('frequenza', $request->frequenza);
        }

        $tuttiPagamenti = $query->get();

        // Filtra i pagamenti che cadono nel mese visualizzato
        $pagamenti = $tuttiPagamenti->filter(function($pagamento) use ($dataInizio, $dataFine) {
            // Usa data_inizio come punto di partenza per le ricorrenze
            $primaRicorrenza = Carbon::parse($pagamento->data_inizio);
            
            // Se il pagamento non è ancora iniziato, skip
            if ($primaRicorrenza > $dataFine) {
                return false;
            }
            
            // Per ogni pagamento periodico, calcola se cade nel mese visualizzato
            $interval = null;
            switch($pagamento->frequenza) {
                case 'mensile':
                    $interval = 1;
                    break;
                case 'trimestrale':
                    $interval = 3;
                    break;
                case 'annuale':
                    $interval = 12;
                    break;
            }

            if (!$interval) {
                return false;
            }

            // Calcola quanti mesi sono passati dalla prima ricorrenza
            $mesiDallaPartenza = $primaRicorrenza->diffInMonths($dataInizio, false);
            
            // Se il mese visualizzato è prima della data_inizio, skip
            if ($mesiDallaPartenza < 0) {
                return false;
            }
            
            // Calcola se questo mese è una ricorrenza valida
            if ($mesiDallaPartenza % $interval === 0) {
                // Calcola la data esatta della ricorrenza in questo mese
                $ricorrenzaDelMese = $primaRicorrenza->copy()->addMonths($mesiDallaPartenza);
                
                // Verifica che la ricorrenza cada effettivamente nel mese visualizzato
                // E che non superi la data_scadenza del contratto
                $dataScadenzaContratto = Carbon::parse($pagamento->data_scadenza);
                
                if ($ricorrenzaDelMese >= $dataInizio && 
                    $ricorrenzaDelMese <= $dataFine && 
                    $ricorrenzaDelMese <= $dataScadenzaContratto) {
                    $pagamento->data_scadenza_calcolata = $ricorrenzaDelMese;
                    return true;
                }
            }

            return false;
        })->sortBy('data_scadenza_calcolata')->values();

        // Calcola totali e conteggi per il mese corrente
        $totali = [
            'in_sospeso' => $pagamenti->where('stato', 'in_sospeso')->sum('importo'),
            'pagato' => $pagamenti->where('stato', 'pagato')->sum('importo'),
            'annullato' => $pagamenti->where('stato', 'annullato')->sum('importo'),
        ];

        $conteggi = [
            'in_sospeso' => $pagamenti->where('stato', 'in_sospeso')->count(),
            'pagato' => $pagamenti->where('stato', 'pagato')->count(),
            'annullato' => $pagamenti->where('stato', 'annullato')->count(),
        ];

        // Date per navigazione
        $mesePrecedente = $dataInizio->copy()->subMonth()->format('Y-m');
        $meseSuccessivo = $dataInizio->copy()->addMonth()->format('Y-m');
        $meseFormattato = $dataInizio->locale('it')->isoFormat('MMMM YYYY');

        // Clienti per filtro
        $clienti = Cliente::orderBy('nome')->get();

        return view('pagamenti.periodici.index', compact(
            'pagamenti', 
            'totali', 
            'conteggi', 
            'mese', 
            'mesePrecedente', 
            'meseSuccessivo',
            'meseFormattato',
            'clienti'
        ));
    }
}
