@extends('layouts.app')

@section('title', 'Tasks')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-check2-square me-2"></i>Tasks</h1>
    <a href="{{ route('tasks.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nuovo Task
    </a>
</div>

<!-- Filtro per Lavoro -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('tasks.index') }}" class="row g-3">
            <div class="col-md-10">
                <label for="lavoro_id" class="form-label">Filtra per Lavoro</label>
                <select id="lavoro_id" name="lavoro_id" class="form-select">
                    <option value="">Tutti i lavori</option>
                    @foreach($lavori as $lavoro)
                        <option value="{{ $lavoro->id }}" {{ $lavoroId == $lavoro->id ? 'selected' : '' }}>
                            {{ $lavoro->descrizione }} ({{ $lavoro->cliente->nome ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-filter"></i> Filtra
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tasks Board stile Trello -->
<div class="row">
    @forelse($tasks as $task)
        <div class="col-md-4 col-lg-3 mb-4">
            <div class="task-card {{ $task->isInRitardo() ? 'task-card-ritardo' : ($task->status == 'completato' ? 'task-card-completato' : 'task-card-sospeso') }}">
                <div class="task-card-header">
                    <h5 class="task-title">{{ $task->nome }}</h5>
                    <div class="task-actions">
                        <a href="{{ route('tasks.edit', $task) }}" 
                           class="btn btn-warning btn-sm me-1" 
                           title="Modifica">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                        <form action="{{ route('tasks.destroy', $task) }}" 
                              method="POST" 
                              class="d-inline"
                              onsubmit="return confirm('Sei sicuro di voler eliminare questo task?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" title="Elimina">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="task-card-body">
                    <div class="task-info">
                        <small class="text-muted">
                            <i class="bi bi-briefcase"></i> {{ $task->lavoro->descrizione }}
                        </small>
                    </div>
                    <div class="task-info mt-2">
                        <small class="text-muted">
                            <i class="bi bi-person"></i> {{ $task->lavoro->cliente->nome ?? 'N/A' }}
                        </small>
                    </div>
                    <div class="task-info mt-2">
                        <small class="text-muted">
                            <i class="bi bi-calendar"></i> Scadenza: {{ $task->scadenza->format('d/m/Y') }}
                        </small>
                    </div>
                    
                    <div class="mt-3">
                        @if($task->status == 'in_sospeso')
                            <span class="badge bg-warning text-dark">In Sospeso</span>
                            @if($task->isInRitardo())
                                <span class="badge bg-danger ms-1">In Ritardo</span>
                            @endif
                        @else
                            <span class="badge bg-success">Completato</span>
                        @endif
                    </div>
                </div>
                
                @if($task->status == 'in_sospeso')
                    <div class="task-card-footer">
                        <form action="{{ route('tasks.completa', $task) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm w-100">
                                <i class="bi bi-check-lg me-1"></i>Segna Completato
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>Nessun task trovato. Crea il tuo primo task!
            </div>
        </div>
    @endforelse
</div>
@endsection
