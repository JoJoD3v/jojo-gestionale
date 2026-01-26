@extends('layouts.guest')

@section('content')
    <div class="mb-4 text-muted">
        Questa è un'area sicura dell'applicazione. Per favore, conferma la tua password prima di continuare.
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div class="form-outline mb-4">
            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                   required autocomplete="current-password" />
            <label class="form-label" for="password">Password</label>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">
                Conferma
            </button>
        </div>
    </form>
@endsection
