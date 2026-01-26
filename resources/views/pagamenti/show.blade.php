@extends('layouts.app')

@section('title', 'Dettagli Pagamento')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Dettagli Pagamento</h4>
                <div>
                    @if($pagamento->stato == 'in_sospeso')
                        <form action="{{ route('pagamenti.marca-pagato', $pagamento) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm me-2">
                                <i class="bi bi-check-lg me-1"></i>Segna come Pagato
                            </button>
                        </form>
                        <form action="{{ route('pagamenti.annulla', $pagamento) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm me-2">
                                <i class="bi bi-x-lg me-1"></i>Annulla
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('pagamenti.edit', $pagamento) }}" class="btn btn-light btn-sm me-2">
                        <i class="bi bi-pencil me-1"></i>Modifica
                    </a>
                    <a href="{{ route('pagamenti.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Indietro
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Cliente</h6>
                        <p class="mb-3">
                            <a href="{{ route('clienti.show', $pagamento->cliente) }}" class="text-decoration-none">
                                <i class="bi bi-person-circle me-1"></i>
                                <strong>{{ $pagamento->cliente->nome }}</strong>
                            </a>
                        </p>

                        <h6 class="text-muted">Email Cliente</h6>
                        <p class="mb-3">
                            <i class="bi bi-envelope me-1"></i>
                            <a href="mailto:{{ $pagamento->cliente->email }}">{{ $pagamento->cliente->email }}</a>
                        </p>

                        @if($pagamento->cliente->telefono)
                        <h6 class="text-muted">Telefono Cliente</h6>
                        <p class="mb-3">
                            <i class="bi bi-telephone me-1"></i>
                            <a href="tel:{{ $pagamento->cliente->telefono }}">{{ $pagamento->cliente->telefono }}</a>
                        </p>
                        @endif

                        <h6 class="text-muted">Tipo di Lavoro</h6>
                        <p class="mb-3">{{ $pagamento->tipo_lavoro }}</p>
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-muted">Importo</h6>
                        <p class="mb-3">
                            <h3 class="text-success">€ {{ number_format($pagamento->importo, 2, ',', '.') }}</h3>
                        </p>

                        <h6 class="text-muted">Cadenza</h6>
                        <p class="mb-3">
                            @if($pagamento->cadenza == 'oneshot')
                                <span class="badge bg-secondary badge-lg">One-shot (una tantum)</span>
                            @else
                                <span class="badge bg-info badge-lg">
                                    Periodico - {{ ucfirst($pagamento->frequenza) }}
                                </span>
                                @if($pagamento->data_inizio)
                                    <br><small class="text-muted">Inizio: {{ \Carbon\Carbon::parse($pagamento->data_inizio)->format('d/m/Y') }}</small>
                                @endif
                            @endif
                        </p>

                        <h6 class="text-muted">Data Scadenza</h6>
                        <p class="mb-3">
                            <i class="bi bi-calendar-event me-1"></i>
                            <strong>{{ \Carbon\Carbon::parse($pagamento->data_scadenza)->format('d/m/Y') }}</strong>
                            @if($pagamento->data_scadenza < now() && $pagamento->stato == 'in_sospeso')
                                <br><span class="badge bg-danger">Pagamento Scaduto!</span>
                            @else
                                <br><small class="text-muted">{{ \Carbon\Carbon::parse($pagamento->data_scadenza)->locale('it')->diffForHumans() }}</small>
                            @endif
                        </p>

                        <h6 class="text-muted">Stato</h6>
                        <p class="mb-3">
                            <span class="badge badge-{{ $pagamento->stato }} badge-lg">
                                @if($pagamento->stato == 'in_sospeso')
                                    <i class="bi bi-clock-history me-1"></i> In Sospeso
                                @elseif($pagamento->stato == 'pagato')
                                    <i class="bi bi-check-circle me-1"></i> Pagato
                                @else
                                    <i class="bi bi-x-circle me-1"></i> Annullato
                                @endif
                            </span>
                        </p>

                        <h6 class="text-muted">Creato il</h6>
                        <p class="mb-3">
                            <i class="bi bi-clock me-1"></i>
                            {{ $pagamento->created_at->format('d/m/Y H:i') }}
                        </p>

                        @if($pagamento->updated_at != $pagamento->created_at)
                        <h6 class="text-muted">Ultimo aggiornamento</h6>
                        <p class="mb-3">
                            <i class="bi bi-clock-history me-1"></i>
                            {{ $pagamento->updated_at->format('d/m/Y H:i') }}
                        </p>
                        @endif
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex gap-2">
                    @if($pagamento->stato == 'in_sospeso')
                        <form action="{{ route('pagamenti.marca-pagato', $pagamento) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-lg me-1"></i>Segna come Pagato
                            </button>
                        </form>
                        <form action="{{ route('pagamenti.annulla', $pagamento) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-x-lg me-1"></i>Annulla Pagamento
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('pagamenti.edit', $pagamento) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i>Modifica
                    </a>
                    <form action="{{ route('pagamenti.destroy', $pagamento) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="btn btn-secondary" 
                                onclick="return confirm('Sei sicuro di voler eliminare questo pagamento? Questa azione non può essere annullata.')">
                            <i class="bi bi-trash me-1"></i>Elimina
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
