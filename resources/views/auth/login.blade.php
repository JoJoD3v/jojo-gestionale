@extends('layouts.guest')

@section('content')
    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success mb-4" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="form-outline mb-4">
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                   value="{{ old('email') }}" required autofocus autocomplete="username" />
            <label class="form-label" for="email">Email</label>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-outline mb-4">
            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                   required autocomplete="current-password" />
            <label class="form-label" for="password">Password</label>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
            <label class="form-check-label" for="remember_me">
                Ricordami
            </label>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
                Accedi
            </button>
        </div>
    </form>
@endsection
