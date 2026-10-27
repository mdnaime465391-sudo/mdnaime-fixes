@extends('layouts.auth')

@section('title', 'Reset password')

@section('content')
    <p class="eyebrow">Password help</p>
    <h1>Reset your password.</h1>
    <p class="sub">Enter the admin email and we will send a single-use reset link.</p>

    <form method="POST" action="{{ route('password.email') }}" data-lock-submit>
        @csrf

        <div class="field">
            <label class="label" for="email">Email</label>
            <input class="input @error('email') is-invalid @enderror" id="email" name="email" type="email"
                   value="{{ old('email') }}" autocomplete="username" required autofocus>
            @error('email')<span class="error">{{ $message }}</span>@enderror
        </div>

        <button class="abtn abtn-primary abtn-lg abtn-block" type="submit">Send reset link</button>
    </form>

    <div class="auth-links">
        <a class="link" href="{{ route('login') }}">← Back to sign in</a>
    </div>
@endsection
