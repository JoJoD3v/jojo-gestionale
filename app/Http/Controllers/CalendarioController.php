<?php

namespace App\Http\Controllers;

use App\Models\Lavoro;
use App\Models\Pagamento;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarioController extends Controller
{
    /**
     * Mostra la vista del calendario
     */
    public function index()
    {
        return view('calendario.index');
    }

    /**
     * API per ottenere gli eventi del calendario (lavori e pagamenti)
     */
    public function getEventi(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');

        $eventi = [];

        // Lavori
        $lavori = Lavoro::with('cliente')
            ->whereBetween('data_lavoro', [$start, $end])
            ->get();

        foreach ($lavori as $lavoro) {
            $eventi[] = [
                'id' => 'lavoro-' . $lavoro->id,
                'title' => '💼 ' . $lavoro->cliente->nome,
                'start' => $lavoro->data_lavoro,
                'className' => 'fc-event-lavoro',
                'extendedProps' => [
                    'tipo' => 'lavoro',
                    'descrizione' => $lavoro->descrizione,
                    'stato' => $lavoro->stato,
                ]
            ];
        }

        // Pagamenti
        $pagamenti = Pagamento::with('cliente')
            ->whereBetween('data_scadenza', [$start, $end])
            ->get();

        foreach ($pagamenti as $pagamento) {
            $className = 'fc-event-pagamento-' . str_replace('_', '-', $pagamento->stato);

            $eventi[] = [
                'id' => 'pagamento-' . $pagamento->id,
                'title' => '💰 ' . $pagamento->cliente->nome . ' - €' . number_format($pagamento->importo, 2, ',', '.'),
                'start' => $pagamento->data_scadenza,
                'className' => $className,
                'extendedProps' => [
                    'tipo' => 'pagamento',
                    'tipo_lavoro' => $pagamento->tipo_lavoro,
                    'importo' => $pagamento->importo,
                    'stato' => $pagamento->stato,
                ]
            ];
        }

        return response()->json($eventi);
    }

    /**
     * API per ottenere i dettagli di un giorno specifico
     */
    public function getDettagliGiorno(Request $request)
    {
        $data = Carbon::parse($request->input('data'));

        $lavori = Lavoro::with('cliente')
            ->whereDate('data_lavoro', $data)
            ->get()
            ->map(function($lavoro) {
                return [
                    'id' => $lavoro->id,
                    'cliente' => [
                        'nome' => $lavoro->cliente->nome,
                    ],
                    'descrizione' => $lavoro->descrizione,
                    'stato' => $lavoro->stato,
                    'stato_label' => ucfirst(str_replace('_', ' ', $lavoro->stato)),
                ];
            });

        $pagamenti = Pagamento::with('cliente')
            ->whereDate('data_scadenza', $data)
            ->get()
            ->map(function($pagamento) {
                return [
                    'id' => $pagamento->id,
                    'cliente' => [
                        'nome' => $pagamento->cliente->nome,
                    ],
                    'tipo_lavoro' => $pagamento->tipo_lavoro,
                    'importo' => $pagamento->importo,
                    'importo_formattato' => number_format($pagamento->importo, 2, ',', '.'),
                    'stato' => $pagamento->stato,
                    'stato_label' => ucfirst(str_replace('_', ' ', $pagamento->stato)),
                ];
            });

        return response()->json([
            'lavori' => $lavori,
            'pagamenti' => $pagamenti,
        ]);
    }
}

