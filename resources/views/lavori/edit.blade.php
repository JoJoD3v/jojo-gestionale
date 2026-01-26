@extends('layouts.app')

@section('title', 'Modifica Lavoro')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0"><i class="bi bi-pencil me-2"></i>Modifica Lavoro</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('lavori.update', $lavoro) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Cliente -->
                    <div class="mb-4">
                        <label for="cliente_id" class="form-label">Cliente *</label>
                        <select id="cliente_id" 
                                name="cliente_id" 
                                class="form-select @error('cliente_id') is-invalid @enderror" 
                                required>
                            <option value="">Seleziona un cliente</option>
                            @foreach($clienti as $cliente)
                                <option value="{{ $cliente->id }}" 
                                        {{ old('cliente_id', $lavoro->cliente_id) == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nome }}
                                </option>
                            @endforeach
                        </select>
                        @error('cliente_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Data Lavoro -->
                    <div class="mb-4">
                        <label class="form-label" for="data_lavoro">Data Lavoro *</label>
                        <input type="date" 
                               id="data_lavoro" 
                               name="data_lavoro" 
                               class="form-control @error('data_lavoro') is-invalid @enderror" 
                               value="{{ old('data_lavoro', $lavoro->data_lavoro) }}" 
                               required />
                        @error('data_lavoro')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Descrizione -->
                    <div class="mb-4">
                        <label class="form-label" for="descrizione">Descrizione Lavori da Svolgere *</label>
                        <textarea id="descrizione" 
                                  name="descrizione" 
                                  class="form-control @error('descrizione') is-invalid @enderror" 
                                  rows="6"
                                  placeholder="Es: Manutenzione ordinaria, riparazione, installazione..."
                                  required>{{ old('descrizione', $lavoro->descrizione) }}</textarea>
                        @error('descrizione')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Stato -->
                    <div class="mb-4">
                        <label class="form-label d-block">Stato</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" 
                                   type="radio" 
                                   name="stato" 
                                   id="stato_da_fare" 
                                   value="da_fare" 
                                   {{ old('stato', $lavoro->stato) == 'da_fare' ? 'checked' : '' }}>
                            <label class="form-check-label" for="stato_da_fare">
                                Da Fare
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" 
                                   type="radio" 
                                   name="stato" 
                                   id="stato_in_corso" 
                                   value="in_corso" 
                                   {{ old('stato', $lavoro->stato) == 'in_corso' ? 'checked' : '' }}>
                            <label class="form-check-label" for="stato_in_corso">
                                In Corso
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" 
                                   type="radio" 
                                   name="stato" 
                                   id="stato_completato" 
                                   value="completato" 
                                   {{ old('stato', $lavoro->stato) == 'completato' ? 'checked' : '' }}>
                            <label class="form-check-label" for="stato_completato">
                                Completato
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('lavori.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Indietro
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-save me-1"></i>Aggiorna Lavoro
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.form-outline').forEach((formOutline) => {
        new mdb.Input(formOutline).init();
    });
</script>
@endpush
