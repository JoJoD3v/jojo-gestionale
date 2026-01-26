@extends('layouts.guest')

@section('content')
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="form-outline mb-4">
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                   value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" />
            <label class="form-label" for="email">Email</label>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-outline mb-4">
            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                   required autocomplete="new-password" />
            <label class="form-label" for="password">Nuova Password</label>
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

        <div class="text-end">
            <button type="submit" class="btn btn-primary">
                Reimposta Password
            </button>
        </div>
    </form>
@endsection
