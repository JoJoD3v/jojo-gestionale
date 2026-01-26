@extends('layouts.app')

@section('title', 'Nuovo Cliente')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="bi bi-person-plus me-2"></i>Nuovo Cliente</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('clienti.store') }}" method="POST">
                    @csrf

                    <!-- Nome -->
                    <div class="mb-4">
                        <label class="form-label" for="nome">Nome / Ragione Sociale *</label>
                        <input type="text" 
                               id="nome" 
                               name="nome" 
                               class="form-control @error('nome') is-invalid @enderror" 
                               value="{{ old('nome') }}" 
                               placeholder="Es: Mario Rossi"
                               required />
                        @error('nome')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label class="form-label" for="email">Email *</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email') }}" 
                               placeholder="email@esempio.it"
                               required />
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Telefono -->
                    <div class="mb-4">
                        <label class="form-label" for="telefono">Telefono</label>
                        <input type="text" 
                               id="telefono" 
                               name="telefono" 
                               class="form-control @error('telefono') is-invalid @enderror" 
                               value="{{ old('telefono') }}" 
                               placeholder="+39 123 456 7890" />
                        @error('telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Partita IVA -->
                    <div class="mb-4">
                        <label class="form-label" for="partita_iva">Partita IVA / Codice Fiscale</label>
                        <input type="text" 
                               id="partita_iva" 
                               name="partita_iva" 
                               class="form-control @error('partita_iva') is-invalid @enderror" 
                               value="{{ old('partita_iva') }}" 
                               placeholder="IT12345678901" />
                        @error('partita_iva')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Note -->
                    <div class="mb-4">
                        <label class="form-label" for="note">Note</label>
                        <textarea id="note" 
                                  name="note" 
                                  class="form-control @error('note') is-invalid @enderror" 
                                  rows="4"
                                  placeholder="Note aggiuntive sul cliente...">{{ old('note') }}</textarea>
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('clienti.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Indietro
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Salva Cliente
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
    // Initialize MDB form validation
    document.querySelectorAll('.form-outline').forEach((formOutline) => {
        new mdb.Input(formOutline).init();
    });
</script>
@endpush
