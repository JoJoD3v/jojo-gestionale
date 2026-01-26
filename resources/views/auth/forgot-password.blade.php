@extends('layouts.guest')

@section('content')
    <div class="mb-4 text-muted">
        Password dimenticata? Nessun problema. Inserisci il tuo indirizzo email e ti invieremo un link per reimpostare la password.
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success mb-4" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="form-outline mb-4">
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                   value="{{ old('email') }}" required autofocus />
            <label class="form-label" for="email">Email</label>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('login') }}" class="text-primary">
                Torna al login
            </a>
            
            <button type="submit" class="btn btn-primary">
                Invia Link
            </button>
        </div>
    </form>
@endsection
