<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Sign in') — Md Naime Admin</title>
    <script>
        (function () {
            try {
                var saved = localStorage.getItem('admin-theme');
                if (saved) { document.documentElement.setAttribute('data-theme', saved); }
            } catch (e) {}
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Instrument+Sans:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
</head>
<body>
<div class="auth-shell">
    <section class="auth-brand" aria-label="Md Naime studio">
        <a class="brand" href="{{ route('home') }}" style="border-bottom:0;padding:0">
            <span class="brand-mark" aria-hidden="true">N</span>
            <span class="brand-copy">
                <strong>Md Naime</strong>
                <span>Studio admin</span>
            </span>
        </a>

        <div>
            <p class="eyebrow" style="margin-bottom:18px">Private workspace</p>
            <h2 class="auth-statement">The room behind <em>the work.</em></h2>
            <p class="auth-sub">Projects, services, the About page and every inquiry — one quiet place to keep the studio current.</p>
        </div>

        <div class="auth-meta">
            <span>Single admin</span>
            <span>Throttled login</span>
            <span>Session protected</span>
        </div>
    </section>

    <section class="auth-panel">
        <div class="auth-form">
            @include('admin.partials.flash')
            @yield('content')
            <p class="auth-foot">© {{ now()->year }} Md Naime. Access is logged and rate limited.</p>
        </div>
    </section>
</div>
@php($adminJsVersion = @filemtime(public_path('assets/js/admin.js')) ?: 1)
<script src="{{ asset('assets/js/admin.js') }}?v={{ $adminJsVersion }}" defer></script>
</body>
</html>
