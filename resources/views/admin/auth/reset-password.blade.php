@extends('layouts.auth')

@section('title', 'Choose a new password')

@section('content')
    <p class="eyebrow">Password reset</p>
    <h1>Choose a new password.</h1>
    <p class="sub">Use at least 12 characters with upper and lower case letters and a number.</p>

    <form method="POST" action="{{ route('password.store') }}" data-lock-submit>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="field">
            <label class="label" for="email">Email</label>
            <input class="input @error('email') is-invalid @enderror" id="email" name="email" type="email"
                   value="{{ old('email', $email) }}" autocomplete="username" required>
            @error('email')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="field">
            <label class="label" for="password">New password</label>
            <input class="input @error('password') is-invalid @enderror" id="password" name="password" type="password"
                   autocomplete="new-password" required>
            @error('password')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="field">
            <label class="label" for="password_confirmation">Confirm password</label>
            <input class="input" id="password_confirmation" name="password_confirmation" type="password"
                   autocomplete="new-password" required>
        </div>

        <button class="abtn abtn-primary abtn-lg abtn-block" type="submit">Update password</button>
    </form>

    <div class="auth-links">
        <a class="link" href="{{ route('login') }}">← Back to sign in</a>
    </div>
@endsection
