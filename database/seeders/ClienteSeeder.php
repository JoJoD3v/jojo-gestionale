<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clienti = [
            [
                'nome' => 'Mario Rossi',
                'email' => 'mario.rossi@example.com',
                'telefono' => '+39 340 1234567',
                'partita_iva' => 'IT12345678901',
                'note' => 'Cliente storico, lavori di sviluppo web',
            ],
            [
                'nome' => 'Tech Solutions SRL',
                'email' => 'info@techsolutions.it',
                'telefono' => '+39 02 12345678',
                'partita_iva' => 'IT98765432109',
                'note' => 'Azienda di consulenza IT',
            ],
            [
                'nome' => 'Giovanni Bianchi',
                'email' => 'g.bianchi@email.it',
                'telefono' => '+39 333 9876543',
                'partita_iva' => null,
                'note' => null,
            ],
            [
                'nome' => 'Digital Marketing Italia',
                'email' => 'contact@digitalmarketing.it',
                'telefono' => '+39 06 98765432',
                'partita_iva' => 'IT11223344556',
                'note' => 'Agenzia di marketing digitale',
            ],
            [
                'nome' => 'Laura Verdi',
                'email' => 'laura.verdi@gmail.com',
                'telefono' => '+39 349 5555555',
                'partita_iva' => 'IT66778899001',
                'note' => 'Freelance designer grafica',
            ],
        ];

        foreach ($clienti as $cliente) {
            Cliente::create($cliente);
        }
    }
}
