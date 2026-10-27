<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'Md Naime') }} Admin</title>
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
<a class="skip-link" href="#admin-content">Skip to content</a>

<div class="admin-shell">
    @include('admin.partials.sidebar')

    <div class="admin-main">
        @include('admin.partials.topbar')

        <main class="admin-content" id="admin-content">
            @include('admin.partials.flash')
            @yield('content')
        </main>
    </div>
</div>

<div class="sidebar-scrim" data-sidebar-scrim></div>

@php($adminJsVersion = @filemtime(public_path('assets/js/admin.js')) ?: 1)
<script src="{{ asset('assets/js/admin.js') }}?v={{ $adminJsVersion }}" defer></script>
@stack('scripts')
</body>
</html>
