@extends('layouts.app')

@section('title', 'Nuovo Task')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-plus-circle me-2"></i>Nuovo Task</h1>
    <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>Torna alla Lista
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('tasks.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="nome" class="form-label">Nome Task *</label>
                <input type="text" 
                       id="nome" 
                       name="nome" 
                       class="form-control @error('nome') is-invalid @enderror" 
                       value="{{ old('nome') }}" 
                       placeholder="Es: Implementare feature X"
                       required>
                @error('nome')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="lavoro_id" class="form-label">Lavoro *</label>
                <select id="lavoro_id" name="lavoro_id" class="form-select @error('lavoro_id') is-invalid @enderror" required>
                    <option value="">Seleziona un lavoro...</option>
                    @foreach($lavori as $lavoro)
                        <option value="{{ $lavoro->id }}" {{ old('lavoro_id') == $lavoro->id ? 'selected' : '' }}>
                            {{ $lavoro->descrizione }} ({{ $lavoro->cliente->nome ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
                @error('lavoro_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="scadenza" class="form-label">Scadenza *</label>
                <input type="date" 
                       id="scadenza" 
                       name="scadenza" 
                       class="form-control @error('scadenza') is-invalid @enderror" 
                       value="{{ old('scadenza') }}" 
                       required>
                @error('scadenza')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-lg me-1"></i>Annulla
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Crea Task
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
