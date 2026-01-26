@extends('layouts.guest')

@section('content')
    <div class="mb-4 text-muted">
        Grazie per esserti registrato! Prima di iniziare, potresti verificare il tuo indirizzo email cliccando sul link che ti abbiamo appena inviato? Se non hai ricevuto l'email, saremo lieti di inviartene un'altra.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success mb-4" role="alert">
            Un nuovo link di verifica è stato inviato all'indirizzo email che hai fornito durante la registrazione.
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <button type="submit" class="btn btn-primary">
                Reinvia Email di Verifica
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="btn btn-link text-muted">
                Esci
            </button>
        </form>
    </div>
@endsection
