<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lavoro;
use App\Models\Cliente;
use Carbon\Carbon;

class LavoroSeeder extends Seeder
{
    public function run(): void
    {
        $clienti = Cliente::all();

        if ($clienti->isEmpty()) {
            return;
        }

        $lavori = [
            [
                'cliente_id' => $clienti[0]->id,
                'data_lavoro' => Carbon::now()->addDays(5),
                'descrizione' => 'Sviluppo website e-commerce con Laravel e Vue.js',
                'stato' => 'in_corso',
            ],
            [
                'cliente_id' => $clienti[1]->id,
                'data_lavoro' => Carbon::now()->addDays(10),
                'descrizione' => 'Consulenza per architettura microservizi',
                'stato' => 'da_fare',
            ],
            [
                'cliente_id' => $clienti[0]->id,
                'data_lavoro' => Carbon::now()->subDays(3),
                'descrizione' => 'Manutenzione sistema gestionale',
                'stato' => 'completato',
            ],
            [
                'cliente_id' => $clienti[2]->id,
                'data_lavoro' => Carbon::now()->addDays(2),
                'descrizione' => 'Creazione landing page responsive',
                'stato' => 'in_corso',
            ],
            [
                'cliente_id' => $clienti[3]->id,
                'data_lavoro' => Carbon::now()->addDays(7),
                'descrizione' => 'Integrazione API social media',
                'stato' => 'da_fare',
            ],
            [
                'cliente_id' => $clienti[4]->id,
                'data_lavoro' => Carbon::now()->addDays(1),
                'descrizione' => 'Sviluppo portfolio personale con animazioni',
                'stato' => 'in_corso',
            ],
            [
                'cliente_id' => $clienti[1]->id,
                'data_lavoro' => Carbon::now()->subDays(10),
                'descrizione' => 'Migrazione database da MySQL a PostgreSQL',
                'stato' => 'completato',
            ],
            [
                'cliente_id' => $clienti[3]->id,
                'data_lavoro' => Carbon::now()->addDays(15),
                'descrizione' => 'Setup server e deployment automatizzato',
                'stato' => 'da_fare',
            ],
            [
                'cliente_id' => $clienti[2]->id,
                'data_lavoro' => Carbon::now()->addDays(20),
                'descrizione' => 'Ottimizzazione SEO e performance website',
                'stato' => 'da_fare',
            ],
            [
                'cliente_id' => $clienti[4]->id,
                'data_lavoro' => Carbon::now()->subDays(5),
                'descrizione' => 'Correzione bug e aggiornamento sicurezza',
                'stato' => 'completato',
            ],
        ];

        foreach ($lavori as $lavoro) {
            Lavoro::create($lavoro);
        }
    }
}
