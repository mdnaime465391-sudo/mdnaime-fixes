@php
    $siteTitle = $settings?->site_title ?: config('app.name', 'Md Naime');
    $pageTitle = filled($title ?? null)
        ? $title.' — '.$siteTitle
        : ($settings?->seo_title ?: $siteTitle.' — Med Spa Websites & Laravel Web Apps');
    $pageDescription = $description ?? $settings?->seo_description ?? $settings?->tagline ?? 'Premium med spa websites and the Laravel systems behind them.';
    $shareImage = $ogImage ?? $settings?->ogImageUrl() ?? asset('assets/img/portrait.jpg');
    $canonical = url()->current();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="author" content="{{ $siteTitle }}">
    <meta name="theme-color" content="oklch(12.4% 0.006 66)">
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:type" content="{{ $metaType ?? 'website' }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:image" content="{{ $shareImage }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:site_name" content="{{ $siteTitle }}">
    <meta name="twitter:card" content="summary_large_image">
    @if ($settings?->faviconUrl())
        <link rel="icon" href="{{ $settings->faviconUrl() }}">
    @else
        <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='7' fill='oklch(0.12 0.006 66)'/%3E%3Ctext x='16' y='23' text-anchor='middle' font-family='Georgia,serif' font-size='20' fill='oklch(0.8 0.125 76)'%3EN%3C/text%3E%3C/svg%3E">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Instrument+Sans:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/portfolio.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/site.css') }}">
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'ProfessionalService',
            'name' => $siteTitle,
            'description' => $settings?->seo_description ?: $settings?->tagline,
            'url' => url('/'),
            'email' => $settings?->email,
            'telephone' => $settings?->phone,
            'image' => $shareImage,
            'areaServed' => 'Worldwide',
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => $settings?->location ?: 'Dhaka',
                'addressCountry' => 'BD',
            ],
            'knowsAbout' => [
                'Med spa website design', 'Laravel web applications', 'Booking systems',
                'Client portals', 'Admin dashboards', 'Figma UI/UX design',
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
    </script>
    @stack('head')
    <noscript><style>.reveal,.reveal-img{opacity:1!important;transform:none!important}.split .word-in{transform:none!important}.preloader,.curtain,.cursor,.cursor-dot,.grain{display:none!important}</style></noscript>
</head>
<body>
<a class="skip-link" href="#content">Skip to content</a>

<div class="preloader" role="status" aria-live="polite" aria-label="Loading {{ $siteTitle }}">
    <div class="preloader-top">
        <span class="p-brand">{{ $siteTitle }}</span>
        <span class="p-count num">000</span>
    </div>
    <div class="p-bar" aria-hidden="true"><span></span></div>
</div>

<div class="curtain" aria-hidden="true"><span class="curtain-mark">{{ $siteTitle }}</span></div>
<div class="grain" aria-hidden="true"></div>
<div class="preview-float" aria-hidden="true"><div class="preview-inner"></div></div>

<header class="site-nav" data-od-id="topnav">
    <div class="container nav-inner">
        <a class="brand js-nav" href="{{ route('home') }}" aria-label="{{ $siteTitle }} — home">
            @if ($settings?->logoUrl())
                <img class="brand-logo" src="{{ $settings->logoUrl() }}" alt="{{ $siteTitle }}" height="30">
            @else
                <span class="brand-mark" aria-hidden="true">MN</span> {{ $siteTitle }}
            @endif
        </a>
        <nav class="nav-links" aria-label="Primary">
            <a class="js-nav {{ request()->routeIs('projects.*') ? 'is-current' : '' }}" href="{{ route('projects.index') }}">Projects</a>
            <a class="js-nav {{ request()->routeIs('services') ? 'is-current' : '' }}" href="{{ route('services') }}">Services</a>
            <a class="js-nav {{ request()->routeIs('about') ? 'is-current' : '' }}" href="{{ route('about') }}">About</a>
            <a class="js-nav {{ request()->routeIs('contact') ? 'is-current' : '' }}" href="{{ route('contact') }}">Contact</a>
        </nav>
        <div class="nav-actions">
            <a class="btn btn-secondary btn-sm js-nav" href="{{ route('contact') }}" data-magnetic="0.2">Start a project</a>
            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="menu" aria-label="Open menu">
                <span class="bars" aria-hidden="true"><span></span><span></span><span></span></span>
            </button>
        </div>
    </div>
</header>

<div class="menu-overlay" id="menu" aria-hidden="true">
    <nav class="menu-list" aria-label="Mobile">
        <span class="menu-item"><a class="js-nav" href="{{ route('projects.index') }}">Projects</a></span>
        <span class="menu-item"><a class="js-nav" href="{{ route('services') }}">Services</a></span>
        <span class="menu-item"><a class="js-nav" href="{{ route('about') }}">About</a></span>
        <span class="menu-item"><a class="js-nav" href="{{ route('contact') }}">Contact</a></span>
    </nav>
    <div class="menu-foot">
        <div>
            <p class="kicker">WORK WITH ME</p>
            <a class="link-underline js-nav" href="{{ route('contact') }}" style="margin-top:12px">Start a project</a>
        </div>
        <div>
            <p class="kicker">EMAIL</p>
            <a class="link-underline" href="mailto:{{ $settings?->email }}" style="margin-top:12px">{{ $settings?->email }}</a>
        </div>
    </div>
</div>

<main id="content">
    @yield('content')
</main>

<footer class="site-foot" data-od-id="footer">
    <div class="container">
        <div class="foot-top">
            <div>
                <div class="foot-wordmark">{{ $siteTitle }}</div>
                <p class="meta" style="margin-top:var(--gap-md);max-width:34ch">{{ $settings?->tagline }}</p>
                <div class="row-wrap" style="margin-top:var(--gap-lg)">
                    @if ($settings?->availability_label)
                        <span class="pill">{{ $settings->availability_label }}</span>
                    @endif
                    @if ($settings?->location)
                        <span class="pill">{{ $settings->location }}</span>
                    @endif
                </div>
            </div>
            <div class="foot-col">
                <h4>Pages</h4>
                <ul>
                    <li><a class="js-nav" href="{{ route('projects.index') }}">Projects</a></li>
                    <li><a class="js-nav" href="{{ route('services') }}">Services</a></li>
                    <li><a class="js-nav" href="{{ route('about') }}">About</a></li>
                    <li><a class="js-nav" href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </div>
            <div class="foot-col">
                <h4>Services</h4>
                <ul>
                    @foreach ($footerServices as $service)
                        <li><a class="js-nav" href="{{ route('services') }}#{{ $service->slug }}">{{ $service->title }}</a></li>
                    @endforeach
                    <li><a class="js-nav" href="{{ route('projects.index') }}">Case studies</a></li>
                </ul>
            </div>
            <div class="foot-col">
                <h4>Contact</h4>
                <ul>
                    <li><a href="mailto:{{ $settings?->email }}">{{ $settings?->email }}</a></li>
                    @if ($settings?->phone)
                        <li><a href="{{ $settings->telHref() }}">{{ $settings->phone }}</a></li>
                    @endif
                </ul>
                @if (! empty($settings?->social_links))
                    <h4 style="margin-top:var(--gap-lg)">Elsewhere</h4>
                    <ul>
                        @foreach ($settings->social_links as $link)
                            <li><a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer">{{ $link['label'] }} ↗</a></li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
        <div class="foot-bottom">
            <span class="meta">© <span data-year>{{ now()->year }}</span> {{ $siteTitle }} · All rights reserved</span>
            <a class="to-top" href="#" data-to-top>Back to top <span aria-hidden="true">↑</span></a>
        </div>
    </div>
</footer>

@php($siteJsVersion = @filemtime(public_path('assets/js/site.js')) ?: 1)
<script src="{{ asset('assets/js/site.js') }}?v={{ $siteJsVersion }}" defer></script>
@stack('scripts')
</body>
</html>
