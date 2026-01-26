@extends('layouts.app')

@section('title', 'Pagamenti Periodici')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-arrow-repeat me-2"></i>Pagamenti Periodici - {{ $meseFormattato }}</h2>
    <a href="{{ route('pagamenti.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nuovo Pagamento
    </a>
</div>

<!-- Month Navigation -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-4">
                <div class="d-flex gap-2">
                    <a href="{{ route('pagamenti.periodici.index', array_merge(request()->except('mese'), ['mese' => $mesePrecedente])) }}" 
                       class="btn btn-outline-primary">
                        <i class="bi bi-chevron-left"></i> Mese Precedente
                    </a>
                    <a href="{{ route('pagamenti.periodici.index', request()->except('mese')) }}" 
                       class="btn btn-outline-secondary">
                        <i class="bi bi-calendar-today"></i> Oggi
                    </a>
                    <a href="{{ route('pagamenti.periodici.index', array_merge(request()->except('mese'), ['mese' => $meseSuccessivo])) }}" 
                       class="btn btn-outline-primary">
                        Mese Successivo <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-md-8">
                <form action="{{ route('pagamenti.periodici.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="mese" class="form-label">Seleziona Mese</label>
                        <input type="month" class="form-control" id="mese" name="mese" value="{{ $mese }}">
                    </div>
                    <div class="col-md-3">
                        <label for="cliente_id" class="form-label">Cliente</label>
                        <select class="form-select" id="cliente_id" name="cliente_id">
                            <option value="">Tutti i clienti</option>
                            @foreach(\App\Models\Cliente::orderBy('nome')->get() as $cliente)
                                <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="stato" class="form-label">Stato</label>
                        <select class="form-select" id="stato" name="stato">
                            <option value="">Tutti</option>
                            <option value="in_sospeso" {{ request('stato') == 'in_sospeso' ? 'selected' : '' }}>In Sospeso</option>
                            <option value="pagato" {{ request('stato') == 'pagato' ? 'selected' : '' }}>Pagato</option>
                            <option value="annullato" {{ request('stato') == 'annullato' ? 'selected' : '' }}>Annullato</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="frequenza" class="form-label">Frequenza</label>
                        <select class="form-select" id="frequenza" name="frequenza">
                            <option value="">Tutte</option>
                            <option value="mensile" {{ request('frequenza') == 'mensile' ? 'selected' : '' }}>Mensile</option>
                            <option value="trimestrale" {{ request('frequenza') == 'trimestrale' ? 'selected' : '' }}>Trimestrale</option>
                            <option value="annuale" {{ request('frequenza') == 'annuale' ? 'selected' : '' }}>Annuale</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-filter"></i> Filtra
                        </button>
                        @if(request()->hasAny(['cliente_id', 'stato', 'frequenza']))
                            <a href="{{ route('pagamenti.periodici.index', ['mese' => $mese]) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stat-card bg-warning text-dark">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">In Sospeso</h6>
                    <h3 class="mb-0">€ {{ number_format($totali['in_sospeso'], 2, ',', '.') }}</h3>
                    <small>{{ $conteggi['in_sospeso'] }} pagamenti</small>
                </div>
                <i class="bi bi-clock-history" style="font-size: 3rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card bg-success text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">Pagati</h6>
                    <h3 class="mb-0">€ {{ number_format($totali['pagato'], 2, ',', '.') }}</h3>
                    <small>{{ $conteggi['pagato'] }} pagamenti</small>
                </div>
                <i class="bi bi-check-circle" style="font-size: 3rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card bg-danger text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">Annullati</h6>
                    <h3 class="mb-0">€ {{ number_format($totali['annullato'], 2, ',', '.') }}</h3>
                    <small>{{ $conteggi['annullato'] }} pagamenti</small>
                </div>
                <i class="bi bi-x-circle" style="font-size: 3rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Pagamenti Table -->
<div class="card">
    <div class="card-body">
        @if($pagamenti->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Cliente</th>
                            <th>Tipo Lavoro</th>
                            <th>Importo</th>
                            <th>Cadenza</th>
                            <th>Scadenza</th>
                            <th>Stato</th>
                            <th class="text-center">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pagamenti as $pagamento)
                        <tr class="{{ $pagamento->data_scadenza < now() && $pagamento->stato == 'in_sospeso' ? 'table-danger' : '' }}">
                            <td>
                                <a href="{{ route('clienti.show', $pagamento->cliente) }}">
                                    {{ $pagamento->cliente->nome }}
                                </a>
                            </td>
                            <td>{{ Str::limit($pagamento->tipo_lavoro, 40) }}</td>
                            <td><strong>€ {{ number_format($pagamento->importo, 2, ',', '.') }}</strong></td>
                            <td>
                                @if($pagamento->frequenza == 'mensile')
                                    <span class="badge bg-primary">
                                        <i class="bi bi-calendar-month"></i> Mensile
                                    </span>
                                @elseif($pagamento->frequenza == 'trimestrale')
                                    <span class="badge bg-info">
                                        <i class="bi bi-calendar3"></i> Trimestrale
                                    </span>
                                @else
                                    <span class="badge bg-purple text-white">
                                        <i class="bi bi-calendar-range"></i> Annuale
                                    </span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($pagamento->data_scadenza_calcolata ?? $pagamento->data_scadenza)->format('d/m/Y') }}</strong>
                                <br>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($pagamento->data_scadenza_calcolata ?? $pagamento->data_scadenza)->locale('it')->diffForHumans() }}</small>
                                @if(($pagamento->data_scadenza_calcolata ?? $pagamento->data_scadenza) < now() && $pagamento->stato == 'in_sospeso')
                                    <br><small class="text-danger"><strong>Scaduto!</strong></small>
                                @endif
                            </td>
                            <td>
                                @if($pagamento->stato == 'in_sospeso')
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-clock-history"></i> In Sospeso
                                    </span>
                                @elseif($pagamento->stato == 'pagato')
                                    <span class="badge bg-success text-white">
                                        <i class="bi bi-check-circle"></i> Pagato
                                    </span>
                                @else
                                    <span class="badge bg-danger text-white">
                                        <i class="bi bi-x-circle"></i> Annullato
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($pagamento->stato == 'in_sospeso')
                                    <form action="{{ route('pagamenti.marca-pagato', $pagamento) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-sm btn-success" 
                                                title="Segna come Pagato">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('pagamenti.annulla', $pagamento) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger" 
                                                title="Annulla">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('pagamenti.show', $pagamento) }}" 
                                   class="btn btn-sm btn-info" 
                                   title="Visualizza">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('pagamenti.edit', $pagamento) }}" 
                                   class="btn btn-sm btn-warning" 
                                   title="Modifica">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('pagamenti.destroy', $pagamento) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-secondary" 
                                            onclick="return confirm('Sei sicuro di voler eliminare questo pagamento?')"
                                            title="Elimina">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3">Nessun pagamento periodico trovato per {{ $meseFormattato }}.</p>
                <a href="{{ route('pagamenti.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Crea un pagamento
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
