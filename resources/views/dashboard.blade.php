@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-4">
    <h2><i class="bi bi-speedometer2 me-2"></i>Dashboard</h2>
    <p class="text-muted">Benvenuto nel tuo gestionale</p>
</div>

<!-- Statistiche Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stat-card bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">Clienti Totali</h6>
                    <h2 class="mb-0">{{ $totaleClienti }}</h2>
                </div>
                <i class="bi bi-people" style="font-size: 3rem; opacity: 0.3;"></i>
            </div>
            <div class="mt-3">
                <a href="{{ route('clienti.index') }}" class="btn btn-light btn-sm">Vedi tutti</a>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stat-card bg-info text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">Lavori Questo Mese</h6>
                    <h2 class="mb-0">{{ $lavoriMese }}</h2>
                </div>
                <i class="bi bi-briefcase" style="font-size: 3rem; opacity: 0.3;"></i>
            </div>
            <div class="mt-3">
                <a href="{{ route('lavori.index') }}" class="btn btn-light btn-sm">Vedi lavori</a>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stat-card bg-warning text-dark">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">Pagamenti in Sospeso</h6>
                    <h2 class="mb-0">{{ $pagamentiInSospeso }}</h2>
                </div>
                <i class="bi bi-clock-history" style="font-size: 3rem; opacity: 0.3;"></i>
            </div>
            <div class="mt-3">
                <a href="{{ route('pagamenti.index', ['stato' => 'in_sospeso']) }}" class="btn btn-dark btn-sm">Vedi dettagli</a>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stat-card bg-success text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">Da Incassare</h6>
                    <h2 class="mb-0">€ {{ number_format($importoInSospeso, 2, ',', '.') }}</h2>
                </div>
                <i class="bi bi-cash-coin" style="font-size: 3rem; opacity: 0.3;"></i>
            </div>
            <div class="mt-3">
                <a href="{{ route('pagamenti.index') }}" class="btn btn-light btn-sm">Vedi pagamenti</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Prossimi Lavori -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Prossimi Lavori (7 giorni)</h5>
                <a href="{{ route('lavori.create') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-plus-lg"></i>
                </a>
            </div>
            <div class="card-body">
                @if($prossimiLavori->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($prossimiLavori as $lavoro)
                            <a href="{{ route('lavori.show', $lavoro) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $lavoro->cliente->nome }}</h6>
                                        <p class="mb-1 text-muted small">{{ Str::limit($lavoro->descrizione, 50) }}</p>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar3"></i> 
                                            {{ \Carbon\Carbon::parse($lavoro->data_lavoro)->format('d/m/Y') }}
                                        </small>
                                    </div>
                                    <span class="badge badge-{{ $lavoro->stato }}">
                                        {{ ucfirst(str_replace('_', ' ', $lavoro->stato)) }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="mt-3 text-center">
                        <a href="{{ route('lavori.index') }}" class="btn btn-link">Vedi tutti i lavori →</a>
                    </div>
                @else
                    <p class="text-muted text-center py-4">Nessun lavoro programmato nei prossimi 7 giorni.</p>
                    <div class="text-center">
                        <a href="{{ route('lavori.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>Crea nuovo lavoro
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Pagamenti in Scadenza -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Pagamenti in Scadenza (30 giorni)</h5>
                <a href="{{ route('pagamenti.create') }}" class="btn btn-dark btn-sm">
                    <i class="bi bi-plus-lg"></i>
                </a>
            </div>
            <div class="card-body">
                @if($pagamentiInScadenza->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($pagamentiInScadenza as $pagamento)
                            <a href="{{ route('pagamenti.show', $pagamento) }}" 
                               class="list-group-item list-group-item-action {{ $pagamento->data_scadenza < now() ? 'list-group-item-danger' : '' }}">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $pagamento->cliente->nome }}</h6>
                                        <p class="mb-1 text-muted small">{{ Str::limit($pagamento->tipo_lavoro, 40) }}</p>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar3"></i> 
                                            {{ \Carbon\Carbon::parse($pagamento->data_scadenza)->format('d/m/Y') }}
                                            @if($pagamento->data_scadenza < now())
                                                <span class="badge bg-danger ms-2">Scaduto!</span>
                                            @endif
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <strong class="text-success">€ {{ number_format($pagamento->importo, 2, ',', '.') }}</strong>
                                        <br>
                                        @if($pagamento->cadenza == 'periodico')
                                            <small class="badge bg-info">{{ ucfirst($pagamento->frequenza) }}</small>
                                        @else
                                            <small class="badge bg-secondary">One-shot</small>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="mt-3 text-center">
                        <a href="{{ route('pagamenti.index', ['stato' => 'in_sospeso']) }}" class="btn btn-link">
                            Vedi tutti i pagamenti →
                        </a>
                    </div>
                @else
                    <p class="text-muted text-center py-4">Nessun pagamento in scadenza nei prossimi 30 giorni.</p>
                    <div class="text-center">
                        <a href="{{ route('pagamenti.create') }}" class="btn btn-warning">
                            <i class="bi bi-plus-lg me-1"></i>Crea nuovo pagamento
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Azioni Rapide</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('clienti.create') }}" class="btn btn-outline-primary btn-lg w-100">
                            <i class="bi bi-person-plus d-block mb-2" style="font-size: 2rem;"></i>
                            Nuovo Cliente
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('lavori.create') }}" class="btn btn-outline-info btn-lg w-100">
                            <i class="bi bi-briefcase-fill d-block mb-2" style="font-size: 2rem;"></i>
                            Nuovo Lavoro
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('pagamenti.create') }}" class="btn btn-outline-success btn-lg w-100">
                            <i class="bi bi-cash-coin d-block mb-2" style="font-size: 2rem;"></i>
                            Nuovo Pagamento
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('calendario.index') }}" class="btn btn-outline-warning btn-lg w-100">
                            <i class="bi bi-calendar3 d-block mb-2" style="font-size: 2rem;"></i>
                            Vai al Calendario
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
