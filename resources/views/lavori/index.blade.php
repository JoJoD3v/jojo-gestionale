@extends('layouts.app')

@section('title', 'Gestione Lavori')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-briefcase me-2"></i>Gestione Lavori</h2>
    <a href="{{ route('lavori.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nuovo Lavoro
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('lavori.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
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
            <div class="col-md-3">
                <label for="stato" class="form-label">Stato</label>
                <select class="form-select" id="stato" name="stato">
                    <option value="">Tutti gli stati</option>
                    <option value="da_fare" {{ request('stato') == 'da_fare' ? 'selected' : '' }}>Da Fare</option>
                    <option value="in_corso" {{ request('stato') == 'in_corso' ? 'selected' : '' }}>In Corso</option>
                    <option value="completato" {{ request('stato') == 'completato' ? 'selected' : '' }}>Completato</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="data_da" class="form-label">Data Da</label>
                <input type="date" class="form-control" id="data_da" name="data_da" value="{{ request('data_da') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-filter"></i> Filtra
                </button>
                @if(request()->hasAny(['cliente_id', 'stato', 'data_da']))
                    <a href="{{ route('lavori.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Lavori Table -->
<div class="card">
    <div class="card-body">
        @if($lavori->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Data</th>
                            <th>Cliente</th>
                            <th>Descrizione</th>
                            <th>Stato</th>
                            <th class="text-center">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lavori as $lavoro)
                        <tr>
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($lavoro->data_lavoro)->format('d/m/Y') }}</strong>
                                <br>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($lavoro->data_lavoro)->locale('it')->diffForHumans() }}</small>
                            </td>
                            <td>
                                <a href="{{ route('clienti.show', $lavoro->cliente) }}">
                                    {{ $lavoro->cliente->nome }}
                                </a>
                            </td>
                            <td>{{ Str::limit($lavoro->descrizione, 60) }}</td>
                            <td>
                                <span class="badge badge-{{ $lavoro->stato }}">
                                    @if($lavoro->stato == 'da_fare')
                                        <i class="bi bi-circle"></i> Da Fare
                                    @elseif($lavoro->stato == 'in_corso')
                                        <i class="bi bi-arrow-repeat"></i> In Corso
                                    @else
                                        <i class="bi bi-check-circle"></i> Completato
                                    @endif
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('lavori.show', $lavoro) }}" 
                                   class="btn btn-sm btn-info" 
                                   data-mdb-toggle="tooltip" 
                                   title="Visualizza">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('lavori.edit', $lavoro) }}" 
                                   class="btn btn-sm btn-warning" 
                                   data-mdb-toggle="tooltip" 
                                   title="Modifica">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('lavori.destroy', $lavoro) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Sei sicuro di voler eliminare questo lavoro?')"
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
                {{ $lavori->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3">Nessun lavoro trovato.</p>
                <a href="{{ route('lavori.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Crea il primo lavoro
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
