@extends('layouts.app')

@section('title', 'Dettagli Lavoro')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="bi bi-briefcase me-2"></i>Dettagli Lavoro</h4>
                <div>
                    <a href="{{ route('lavori.edit', $lavoro) }}" class="btn btn-light btn-sm me-2">
                        <i class="bi bi-pencil me-1"></i>Modifica
                    </a>
                    <a href="{{ route('lavori.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Indietro
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Cliente</h6>
                        <p class="mb-3">
                            <a href="{{ route('clienti.show', $lavoro->cliente) }}" class="text-decoration-none">
                                <i class="bi bi-person-circle me-1"></i>
                                <strong>{{ $lavoro->cliente->nome }}</strong>
                            </a>
                        </p>

                        <h6 class="text-muted">Email Cliente</h6>
                        <p class="mb-3">
                            <i class="bi bi-envelope me-1"></i>
                            <a href="mailto:{{ $lavoro->cliente->email }}">{{ $lavoro->cliente->email }}</a>
                        </p>

                        @if($lavoro->cliente->telefono)
                        <h6 class="text-muted">Telefono Cliente</h6>
                        <p class="mb-3">
                            <i class="bi bi-telephone me-1"></i>
                            <a href="tel:{{ $lavoro->cliente->telefono }}">{{ $lavoro->cliente->telefono }}</a>
                        </p>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-muted">Data Lavoro</h6>
                        <p class="mb-3">
                            <i class="bi bi-calendar-event me-1"></i>
                            <strong>{{ \Carbon\Carbon::parse($lavoro->data_lavoro)->format('d/m/Y') }}</strong>
                            <br>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($lavoro->data_lavoro)->locale('it')->diffForHumans() }}</small>
                        </p>

                        <h6 class="text-muted">Stato</h6>
                        <p class="mb-3">
                            <span class="badge badge-{{ $lavoro->stato }} badge-lg">
                                @if($lavoro->stato == 'da_fare')
                                    <i class="bi bi-circle me-1"></i> Da Fare
                                @elseif($lavoro->stato == 'in_corso')
                                    <i class="bi bi-arrow-repeat me-1"></i> In Corso
                                @else
                                    <i class="bi bi-check-circle me-1"></i> Completato
                                @endif
                            </span>
                        </p>

                        <h6 class="text-muted">Creato il</h6>
                        <p class="mb-3">
                            <i class="bi bi-clock me-1"></i>
                            {{ $lavoro->created_at->format('d/m/Y H:i') }}
                        </p>

                        @if($lavoro->updated_at != $lavoro->created_at)
                        <h6 class="text-muted">Ultimo aggiornamento</h6>
                        <p class="mb-3">
                            <i class="bi bi-clock-history me-1"></i>
                            {{ $lavoro->updated_at->format('d/m/Y H:i') }}
                        </p>
                        @endif
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="text-muted">Descrizione Lavoro</h6>
                <div class="card bg-light">
                    <div class="card-body">
                        <p class="mb-0" style="white-space: pre-line;">{{ $lavoro->descrizione }}</p>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <a href="{{ route('lavori.edit', $lavoro) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i>Modifica Lavoro
                    </a>
                    <form action="{{ route('lavori.destroy', $lavoro) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="btn btn-danger" 
                                onclick="return confirm('Sei sicuro di voler eliminare questo lavoro? Questa azione non può essere annullata.')">
                            <i class="bi bi-trash me-1"></i>Elimina Lavoro
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
