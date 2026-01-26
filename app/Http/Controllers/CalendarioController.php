<?php

namespace App\Http\Controllers;

use App\Models\Lavoro;
use App\Models\Pagamento;
use App\Models\Task;
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

        // Tasks
        $tasks = Task::with('lavoro.cliente')
            ->whereBetween('scadenza', [$start, $end])
            ->get();

        foreach ($tasks as $task) {
            $className = 'fc-event-task-' . str_replace('_', '-', $task->status);
            if ($task->isInRitardo()) {
                $className .= ' fc-event-task-ritardo';
            }

            $eventi[] = [
                'id' => 'task-' . $task->id,
                'title' => '✓ ' . $task->nome,
                'start' => $task->scadenza->format('Y-m-d'),
                'className' => $className,
                'extendedProps' => [
                    'tipo' => 'task',
                    'lavoro' => $task->lavoro->descrizione,
                    'status' => $task->status,
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

        $tasks = Task::with('lavoro.cliente')
            ->whereDate('scadenza', $data)
            ->get()
            ->map(function($task) {
                return [
                    'id' => $task->id,
                    'nome' => $task->nome,
                    'lavoro' => [
                        'descrizione' => $task->lavoro->descrizione,
                        'cliente' => [
                            'nome' => $task->lavoro->cliente->nome ?? 'N/A',
                        ],
                    ],
                    'status' => $task->status,
                    'status_label' => ucfirst(str_replace('_', ' ', $task->status)),
                    'in_ritardo' => $task->isInRitardo(),
                ];
            });

        return response()->json([
            'lavori' => $lavori,
            'pagamenti' => $pagamenti,
            'tasks' => $tasks,
        ]);
    }
}

