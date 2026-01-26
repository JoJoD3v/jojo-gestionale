<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Lavoro;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $lavori = Lavoro::orderBy('descrizione')->get();
        $lavoroId = $request->get('lavoro_id');
        
        $tasks = Task::with('lavoro.cliente')
            ->when($lavoroId, function($query, $lavoroId) {
                return $query->where('lavoro_id', $lavoroId);
            })
            ->orderBy('scadenza')
            ->get();

        return view('tasks.index', compact('tasks', 'lavori', 'lavoroId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lavori = Lavoro::with('cliente')->orderBy('descrizione')->get();
        return view('tasks.create', compact('lavori'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'lavoro_id' => 'required|exists:lavori,id',
            'scadenza' => 'required|date',
        ]);

        Task::create($validated);

        return redirect()->route('tasks.index', ['lavoro_id' => $validated['lavoro_id']])
            ->with('success', 'Task creato con successo!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $lavori = Lavoro::with('cliente')->orderBy('descrizione')->get();
        return view('tasks.edit', compact('task', 'lavori'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'lavoro_id' => 'required|exists:lavori,id',
            'scadenza' => 'required|date',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index', ['lavoro_id' => $validated['lavoro_id']])
            ->with('success', 'Task aggiornato con successo!');
    }

    /**
     * Marca un task come completato
     */
    public function completa(Task $task)
    {
        $task->update(['status' => 'completato']);

        return redirect()->route('tasks.index', ['lavoro_id' => $task->lavoro_id])
            ->with('success', 'Task completato!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $lavoroId = $task->lavoro_id;
        $task->delete();

        return redirect()->route('tasks.index', ['lavoro_id' => $lavoroId])
            ->with('success', 'Task eliminato con successo!');
    }
}
