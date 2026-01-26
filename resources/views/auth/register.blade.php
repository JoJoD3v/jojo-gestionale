@extends('layouts.guest')

@section('content')
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="form-outline mb-4">
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" 
                   value="{{ old('name') }}" required autofocus autocomplete="name" />
            <label class="form-label" for="name">Nome</label>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="form-outline mb-4">
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                   value="{{ old('email') }}" required autocomplete="username" />
            <label class="form-label" for="email">Email</label>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-outline mb-4">
            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                   required autocomplete="new-password" />
            <label class="form-label" for="password">Password</label>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-outline mb-4">
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" 
                   required autocomplete="new-password" />
            <label class="form-label" for="password_confirmation">Conferma Password</label>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('login') }}" class="text-primary">
                Hai già un account?
            </a>

            <button type="submit" class="btn btn-primary">
                Registrati
            </button>
        </div>
    </form>
@endsection
