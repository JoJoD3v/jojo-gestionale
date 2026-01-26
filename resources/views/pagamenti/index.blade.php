@extends('layouts.app')

@section('title', 'Gestione Pagamenti')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-cash-coin me-2"></i>Gestione Pagamenti</h2>
    <a href="{{ route('pagamenti.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nuovo Pagamento
    </a>
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

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('pagamenti.index') }}" method="GET" class="row g-3">
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
                <label for="cadenza" class="form-label">Cadenza</label>
                <select class="form-select" id="cadenza" name="cadenza">
                    <option value="">Tutte</option>
                    <option value="oneshot" {{ request('cadenza') == 'oneshot' ? 'selected' : '' }}>One-shot</option>
                    <option value="periodico" {{ request('cadenza') == 'periodico' ? 'selected' : '' }}>Periodico</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="data_da" class="form-label">Scadenza Da</label>
                <input type="date" class="form-control" id="data_da" name="data_da" value="{{ request('data_da') }}">
            </div>
            <div class="col-md-2">
                <label for="data_a" class="form-label">Scadenza A</label>
                <input type="date" class="form-control" id="data_a" name="data_a" value="{{ request('data_a') }}">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-filter"></i>
                </button>
            </div>
        </form>
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
                                @if($pagamento->cadenza == 'oneshot')
                                    <span class="badge bg-secondary">One-shot</span>
                                @else
                                    <span class="badge bg-info">
                                        {{ ucfirst($pagamento->frequenza) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($pagamento->data_scadenza)->format('d/m/Y') }}
                                @if($pagamento->data_scadenza < now() && $pagamento->stato == 'in_sospeso')
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
                                                data-mdb-toggle="tooltip" 
                                                title="Segna come Pagato">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('pagamenti.annulla', $pagamento) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger" 
                                                data-mdb-toggle="tooltip" 
                                                title="Annulla">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('pagamenti.show', $pagamento) }}" 
                                   class="btn btn-sm btn-info" 
                                   data-mdb-toggle="tooltip" 
                                   title="Visualizza">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('pagamenti.edit', $pagamento) }}" 
                                   class="btn btn-sm btn-warning" 
                                   data-mdb-toggle="tooltip" 
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

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $pagamenti->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3">Nessun pagamento trovato.</p>
                <a href="{{ route('pagamenti.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Crea il primo pagamento
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    const tooltipTriggerList = document.querySelectorAll('[data-mdb-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new mdb.Tooltip(tooltipTriggerEl))
</script>
@endpush
