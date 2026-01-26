@extends('layouts.app')

@section('title', 'Modifica Pagamento')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0"><i class="bi bi-pencil me-2"></i>Modifica Pagamento</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('pagamenti.update', $pagamento) }}" method="POST" id="pagamentoForm">
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
                                        {{ old('cliente_id', $pagamento->cliente_id) == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nome }}
                                </option>
                            @endforeach
                        </select>
                        @error('cliente_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tipo Lavoro -->
                    <div class="mb-4">
                        <label class="form-label" for="tipo_lavoro">Tipo di Lavoro / Descrizione *</label>
                        <input type="text" 
                               id="tipo_lavoro" 
                               name="tipo_lavoro" 
                               class="form-control @error('tipo_lavoro') is-invalid @enderror" 
                               value="{{ old('tipo_lavoro', $pagamento->tipo_lavoro) }}" 
                               placeholder="Es: Consulenza, Manutenzione mensile, Sviluppo software"
                               required />
                        @error('tipo_lavoro')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Importo -->
                    <div class="mb-4">
                        <label class="form-label" for="importo">Importo (€) *</label>
                        <input type="number" 
                               id="importo" 
                               name="importo" 
                               class="form-control @error('importo') is-invalid @enderror" 
                               value="{{ old('importo', $pagamento->importo) }}" 
                               step="0.01"
                               min="0"
                               placeholder="Es: 150.00"
                               required />
                        @error('importo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Cadenza -->
                    <div class="mb-4">
                        <label class="form-label d-block">Cadenza Pagamento *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" 
                                   type="radio" 
                                   name="cadenza" 
                                   id="cadenza_oneshot" 
                                   value="oneshot" 
                                   {{ old('cadenza', $pagamento->cadenza) == 'oneshot' ? 'checked' : '' }}
                                   onchange="toggleFrequenza()">
                            <label class="form-check-label" for="cadenza_oneshot">
                                One-shot (una tantum)
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" 
                                   type="radio" 
                                   name="cadenza" 
                                   id="cadenza_periodico" 
                                   value="periodico" 
                                   {{ old('cadenza', $pagamento->cadenza) == 'periodico' ? 'checked' : '' }}
                                   onchange="toggleFrequenza()">
                            <label class="form-check-label" for="cadenza_periodico">
                                Periodico
                            </label>
                        </div>
                    </div>

                    <!-- Frequenza (solo se periodico) -->
                    <div class="mb-4" id="frequenzaDiv" style="display: {{ old('cadenza', $pagamento->cadenza) == 'periodico' ? 'block' : 'none' }};">
                        <label for="frequenza" class="form-label">Frequenza</label>
                        <select id="frequenza" 
                                name="frequenza" 
                                class="form-select @error('frequenza') is-invalid @enderror">
                            <option value="">Seleziona frequenza</option>
                            <option value="mensile" {{ old('frequenza', $pagamento->frequenza) == 'mensile' ? 'selected' : '' }}>Mensile</option>
                            <option value="trimestrale" {{ old('frequenza', $pagamento->frequenza) == 'trimestrale' ? 'selected' : '' }}>Trimestrale</option>
                            <option value="annuale" {{ old('frequenza', $pagamento->frequenza) == 'annuale' ? 'selected' : '' }}>Annuale</option>
                        </select>
                        @error('frequenza')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Data Inizio (solo se periodico) -->
                    <div class="mb-4" id="dataInizioDiv" style="display: {{ old('cadenza', $pagamento->cadenza) == 'periodico' ? 'block' : 'none' }};">
                        <label class="form-label" for="data_inizio">Data Inizio</label>
                        <input type="date" 
                               id="data_inizio" 
                               name="data_inizio" 
                               class="form-control @error('data_inizio') is-invalid @enderror" 
                               value="{{ old('data_inizio', $pagamento->data_inizio) }}" />
                        @error('data_inizio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Data Scadenza -->
                    <div class="mb-4">
                        <label class="form-label" for="data_scadenza">Data Scadenza *</label>
                        <input type="date" 
                               id="data_scadenza" 
                               name="data_scadenza" 
                               class="form-control @error('data_scadenza') is-invalid @enderror" 
                               value="{{ old('data_scadenza', $pagamento->data_scadenza) }}" 
                               required />
                        @error('data_scadenza')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Stato -->
                    <div class="mb-4">
                        <label for="stato" class="form-label">Stato</label>
                        <select id="stato" 
                                name="stato" 
                                class="form-select @error('stato') is-invalid @enderror">
                            <option value="in_sospeso" {{ old('stato', $pagamento->stato) == 'in_sospeso' ? 'selected' : '' }}>In Sospeso</option>
                            <option value="pagato" {{ old('stato', $pagamento->stato) == 'pagato' ? 'selected' : '' }}>Pagato</option>
                            <option value="annullato" {{ old('stato', $pagamento->stato) == 'annullato' ? 'selected' : '' }}>Annullato</option>
                        </select>
                        @error('stato')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('pagamenti.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Indietro
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-save me-1"></i>Aggiorna Pagamento
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

    function toggleFrequenza() {
        const cadenza = document.querySelector('input[name="cadenza"]:checked').value;
        const frequenzaDiv = document.getElementById('frequenzaDiv');
        const dataInizioDiv = document.getElementById('dataInizioDiv');
        const frequenzaSelect = document.getElementById('frequenza');
        
        if (cadenza === 'periodico') {
            frequenzaDiv.style.display = 'block';
            dataInizioDiv.style.display = 'block';
            frequenzaSelect.required = true;
        } else {
            frequenzaDiv.style.display = 'none';
            dataInizioDiv.style.display = 'none';
            frequenzaSelect.required = false;
        }
    }

    // Initialize on load
    toggleFrequenza();
</script>
@endpush
