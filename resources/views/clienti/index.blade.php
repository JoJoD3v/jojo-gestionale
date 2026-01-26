@extends('layouts.app')

@section('title', 'Gestione Clienti')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-people me-2"></i>Gestione Clienti</h2>
    <a href="{{ route('clienti.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nuovo Cliente
    </a>
</div>

<!-- Search Form -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('clienti.index') }}" method="GET" class="row g-3">
            <div class="col-md-10">
                <div class="form-outline">
                    <input type="text" id="search" name="search" class="form-control" value="{{ request('search') }}" />
                    <label class="form-label" for="search">Cerca per nome o email</label>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-center">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search"></i> Cerca
                </button>
                @if(request('search'))
                    <a href="{{ route('clienti.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Clienti Table -->
<div class="card">
    <div class="card-body">
        @if($clienti->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Telefono</th>
                            <th>P.IVA/CF</th>
                            <th class="text-center">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clienti as $cliente)
                        <tr>
                            <td>
                                <strong>{{ $cliente->nome }}</strong>
                            </td>
                            <td>{{ $cliente->email }}</td>
                            <td>{{ $cliente->telefono ?? '-' }}</td>
                            <td>{{ $cliente->partita_iva ?? '-' }}</td>
                            <td class="text-center">
                                <a href="{{ route('clienti.show', $cliente) }}" 
                                   class="btn btn-sm btn-info" 
                                   data-mdb-toggle="tooltip" 
                                   title="Visualizza">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('clienti.edit', $cliente) }}" 
                                   class="btn btn-sm btn-warning" 
                                   data-mdb-toggle="tooltip" 
                                   title="Modifica">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('clienti.destroy', $cliente) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Sei sicuro di voler eliminare il cliente {{ addslashes($cliente->nome) }}? Questa azione eliminerà anche tutti i lavori e pagamenti associati.')"
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
                {{ $clienti->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3">Nessun cliente trovato.</p>
                <a href="{{ route('clienti.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Crea il primo cliente
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-mdb-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new mdb.Tooltip(tooltipTriggerEl))
</script>
@endpush
