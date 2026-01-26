<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pagamento;
use App\Models\Cliente;
use Carbon\Carbon;

class PagamentoSeeder extends Seeder
{
    public function run(): void
    {
        $clienti = Cliente::all();

        if ($clienti->isEmpty()) {
            return;
        }

        $pagamenti = [
            // Pagamenti one-shot
            [
                'cliente_id' => $clienti[0]->id,
                'tipo_lavoro' => 'Sviluppo website e-commerce',
                'importo' => 2500.00,
                'cadenza' => 'oneshot',
                'frequenza' => null,
                'data_inizio' => null,
                'data_scadenza' => Carbon::now()->addDays(30),
                'stato' => 'in_sospeso',
            ],
            [
                'cliente_id' => $clienti[2]->id,
                'tipo_lavoro' => 'Landing page responsive',
                'importo' => 800.00,
                'cadenza' => 'oneshot',
                'frequenza' => null,
                'data_inizio' => null,
                'data_scadenza' => Carbon::now()->addDays(15),
                'stato' => 'in_sospeso',
            ],
            [
                'cliente_id' => $clienti[4]->id,
                'tipo_lavoro' => 'Portfolio personale',
                'importo' => 1200.00,
                'cadenza' => 'oneshot',
                'frequenza' => null,
                'data_inizio' => null,
                'data_scadenza' => Carbon::now()->subDays(2),
                'stato' => 'pagato',
            ],
            // Pagamenti periodici mensili
            [
                'cliente_id' => $clienti[1]->id,
                'tipo_lavoro' => 'Consulenza mensile',
                'importo' => 1500.00,
                'cadenza' => 'periodico',
                'frequenza' => 'mensile',
                'data_inizio' => Carbon::now()->startOfMonth(),
                'data_scadenza' => Carbon::now()->addMonth()->day(10),
                'stato' => 'in_sospeso',
            ],
            [
                'cliente_id' => $clienti[3]->id,
                'tipo_lavoro' => 'Gestione social media',
                'importo' => 600.00,
                'cadenza' => 'periodico',
                'frequenza' => 'mensile',
                'data_inizio' => Carbon::now()->subMonths(3)->startOfMonth(),
                'data_scadenza' => Carbon::now()->day(5),
                'stato' => 'in_sospeso',
            ],
            [
                'cliente_id' => $clienti[0]->id,
                'tipo_lavoro' => 'Manutenzione website',
                'importo' => 300.00,
                'cadenza' => 'periodico',
                'frequenza' => 'mensile',
                'data_inizio' => Carbon::now()->subMonths(2)->startOfMonth(),
                'data_scadenza' => Carbon::now()->subMonth()->day(15),
                'stato' => 'pagato',
            ],
            // Pagamenti periodici trimestrali
            [
                'cliente_id' => $clienti[1]->id,
                'tipo_lavoro' => 'Audit sicurezza trimestrale',
                'importo' => 2000.00,
                'cadenza' => 'periodico',
                'frequenza' => 'trimestrale',
                'data_inizio' => Carbon::now()->subMonths(3)->startOfMonth(),
                'data_scadenza' => Carbon::now()->addMonths(2)->day(20),
                'stato' => 'in_sospeso',
            ],
            // Pagamenti periodici annuali
            [
                'cliente_id' => $clienti[3]->id,
                'tipo_lavoro' => 'Hosting e dominio annuale',
                'importo' => 500.00,
                'cadenza' => 'periodico',
                'frequenza' => 'annuale',
                'data_inizio' => Carbon::now()->subYear()->startOfYear(),
                'data_scadenza' => Carbon::now()->addMonths(3)->day(1),
                'stato' => 'in_sospeso',
            ],
            // Altri pagamenti
            [
                'cliente_id' => $clienti[2]->id,
                'tipo_lavoro' => 'Correzione bug urgenti',
                'importo' => 400.00,
                'cadenza' => 'oneshot',
                'frequenza' => null,
                'data_inizio' => null,
                'data_scadenza' => Carbon::now()->subDays(10),
                'stato' => 'pagato',
            ],
            [
                'cliente_id' => $clienti[4]->id,
                'tipo_lavoro' => 'Setup email marketing',
                'importo' => 350.00,
                'cadenza' => 'oneshot',
                'frequenza' => null,
                'data_inizio' => null,
                'data_scadenza' => Carbon::now()->addDays(7),
                'stato' => 'in_sospeso',
            ],
            [
                'cliente_id' => $clienti[0]->id,
                'tipo_lavoro' => 'Formazione team',
                'importo' => 1800.00,
                'cadenza' => 'oneshot',
                'frequenza' => null,
                'data_inizio' => null,
                'data_scadenza' => Carbon::now()->addDays(45),
                'stato' => 'annullato',
            ],
            [
                'cliente_id' => $clienti[1]->id,
                'tipo_lavoro' => 'Migrazione server cloud',
                'importo' => 3000.00,
                'cadenza' => 'oneshot',
                'frequenza' => null,
                'data_inizio' => null,
                'data_scadenza' => Carbon::now()->addDays(60),
                'stato' => 'in_sospeso',
            ],
            [
                'cliente_id' => $clienti[3]->id,
                'tipo_lavoro' => 'App mobile iOS',
                'importo' => 5000.00,
                'cadenza' => 'oneshot',
                'frequenza' => null,
                'data_inizio' => null,
                'data_scadenza' => Carbon::now()->addDays(90),
                'stato' => 'in_sospeso',
            ],
            [
                'cliente_id' => $clienti[2]->id,
                'tipo_lavoro' => 'Consulenza UX/UI',
                'importo' => 900.00,
                'cadenza' => 'oneshot',
                'frequenza' => null,
                'data_inizio' => null,
                'data_scadenza' => Carbon::now()->subDays(5),
                'stato' => 'pagato',
            ],
            [
                'cliente_id' => $clienti[4]->id,
                'tipo_lavoro' => 'Supporto tecnico settimanale',
                'importo' => 200.00,
                'cadenza' => 'periodico',
                'frequenza' => 'mensile',
                'data_inizio' => Carbon::now()->subMonth()->startOfMonth(),
                'data_scadenza' => Carbon::now()->day(25),
                'stato' => 'in_sospeso',
            ],
        ];

        foreach ($pagamenti as $pagamento) {
            Pagamento::create($pagamento);
        }
    }
}
