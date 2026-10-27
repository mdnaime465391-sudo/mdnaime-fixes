@extends('layouts.auth')

@section('title', 'Sign in')

@section('content')
    <p class="eyebrow">Admin access</p>
    <h1>Welcome back.</h1>
    <p class="sub">Sign in to manage projects, services, settings and inquiries.</p>

    <form method="POST" action="{{ route('login.store') }}" data-lock-submit>
        @csrf

        <div class="field">
            <label class="label" for="email">Email</label>
            <input class="input @error('email') is-invalid @enderror" id="email" name="email" type="email"
                   value="{{ old('email') }}" autocomplete="username" required autofocus
                   placeholder="you@studio.com" @error('email') aria-describedby="email-error" @enderror>
            @error('email')<span class="error" id="email-error">{{ $message }}</span>@enderror
        </div>

        <div class="field">
            <label class="label" for="password">
                Password
                <a class="link" href="{{ route('password.request') }}" style="font-size:11px;font-weight:400">Forgot?</a>
            </label>
            <input class="input @error('password') is-invalid @enderror" id="password" name="password" type="password"
                   autocomplete="current-password" required placeholder="••••••••••••">
            @error('password')<span class="error">{{ $message }}</span>@enderror
        </div>

        <label class="check">
            <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
            <span>Keep me signed in on this device</span>
        </label>

        <button class="abtn abtn-primary abtn-lg abtn-block" type="submit">Sign in</button>
    </form>

    <div class="auth-links">
        <a class="link" href="{{ route('home') }}">← Back to the site</a>
        <span class="faint">Protected by CSRF + rate limiting</span>
    </div>
@endsection
