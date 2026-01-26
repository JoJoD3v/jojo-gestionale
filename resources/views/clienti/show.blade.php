@extends('layouts.app')

@section('title', 'Dettagli Cliente')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Informazioni Cliente</h5>
            </div>
            <div class="card-body">
                <h4>{{ $cliente->nome }}</h4>
                
                <div class="mt-3">
                    <p class="mb-2">
                        <i class="bi bi-envelope me-2"></i>
                        <a href="mailto:{{ $cliente->email }}">{{ $cliente->email }}</a>
                    </p>
                    
                    @if($cliente->telefono)
                    <p class="mb-2">
                        <i class="bi bi-telephone me-2"></i>
                        <a href="tel:{{ $cliente->telefono }}">{{ $cliente->telefono }}</a>
                    </p>
                    @endif
                    
                    @if($cliente->partita_iva)
                    <p class="mb-2">
                        <i class="bi bi-card-text me-2"></i>
                        {{ $cliente->partita_iva }}
                    </p>
                    @endif
                </div>

                @if($cliente->note)
                <div class="mt-3">
                    <h6>Note:</h6>
                    <p class="text-muted">{{ $cliente->note }}</p>
                </div>
                @endif

                <div class="mt-4 d-flex gap-2">
                    <a href="{{ route('clienti.edit', $cliente) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil me-1"></i>Modifica
                    </a>
                    <a href="{{ route('clienti.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Indietro
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Lavori Section -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-briefcase me-2"></i>Lavori ({{ $cliente->lavori->count() }})</h5>
                <a href="{{ route('lavori.create', ['cliente_id' => $cliente->id]) }}" class="btn btn-light btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>Nuovo Lavoro
                </a>
            </div>
            <div class="card-body">
                @if($cliente->lavori->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Descrizione</th>
                                    <th>Stato</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cliente->lavori->sortByDesc('data_lavoro')->take(5) as $lavoro)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($lavoro->data_lavoro)->format('d/m/Y') }}</td>
                                    <td>{{ Str::limit($lavoro->descrizione, 50) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $lavoro->stato }}">
                                            {{ ucfirst(str_replace('_', ' ', $lavoro->stato)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('lavori.show', $lavoro) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($cliente->lavori->count() > 5)
                        <div class="text-center mt-2">
                            <a href="{{ route('lavori.index', ['cliente_id' => $cliente->id]) }}" class="btn btn-link">
                                Vedi tutti i lavori →
                            </a>
                        </div>
                    @endif
                @else
                    <p class="text-muted text-center">Nessun lavoro registrato per questo cliente.</p>
                @endif
            </div>
        </div>

        <!-- Pagamenti Section -->
        <div class="card">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Pagamenti ({{ $cliente->pagamenti->count() }})</h5>
                <a href="{{ route('pagamenti.create', ['cliente_id' => $cliente->id]) }}" class="btn btn-light btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>Nuovo Pagamento
                </a>
            </div>
            <div class="card-body">
                @if($cliente->pagamenti->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tipo Lavoro</th>
                                    <th>Importo</th>
                                    <th>Scadenza</th>
                                    <th>Stato</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cliente->pagamenti->sortByDesc('data_scadenza')->take(5) as $pagamento)
                                <tr>
                                    <td>{{ Str::limit($pagamento->tipo_lavoro, 30) }}</td>
                                    <td><strong>€ {{ number_format($pagamento->importo, 2, ',', '.') }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($pagamento->data_scadenza)->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $pagamento->stato }}">
                                            {{ ucfirst(str_replace('_', ' ', $pagamento->stato)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('pagamenti.show', $pagamento) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($cliente->pagamenti->count() > 5)
                        <div class="text-center mt-2">
                            <a href="{{ route('pagamenti.index', ['cliente_id' => $cliente->id]) }}" class="btn btn-link">
                                Vedi tutti i pagamenti →
                            </a>
                        </div>
                    @endif

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="alert alert-warning mb-0">
                                <small>In Sospeso</small>
                                <h6 class="mb-0">€ {{ number_format($cliente->pagamenti->where('stato', 'in_sospeso')->sum('importo'), 2, ',', '.') }}</h6>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-success mb-0">
                                <small>Pagati</small>
                                <h6 class="mb-0">€ {{ number_format($cliente->pagamenti->where('stato', 'pagato')->sum('importo'), 2, ',', '.') }}</h6>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-danger mb-0">
                                <small>Annullati</small>
                                <h6 class="mb-0">€ {{ number_format($cliente->pagamenti->where('stato', 'annullato')->sum('importo'), 2, ',', '.') }}</h6>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-muted text-center">Nessun pagamento registrato per questo cliente.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
