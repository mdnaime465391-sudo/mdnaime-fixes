@extends('layouts.public')

@php
    $marquee = $services->pluck('title')
        ->merge($projects->flatMap(fn ($project) => $project->categories ?? []))
        ->filter()
        ->unique()
        ->take(8)
        ->values();
@endphp

@section('content')
    <section class="section hero" data-od-id="hero" style="padding-top:clamp(120px,13vw,176px);padding-bottom:clamp(112px,12vw,168px)">
        <style>@media (min-width:1025px){[data-od-id="hero"]{width:90vw;margin-inline:auto}}</style>
        <div class="hero-glow" aria-hidden="true"></div>
        <div class="container grid-hero" style="align-items:center">
            <div>
                @if ($settings?->availability_label)
                    <div class="availability reveal" style="margin-bottom:var(--gap-md)">
                        <span class="dot" aria-hidden="true" style="border-color:rgb(132,204,22);color:rgb(132,204,22)"></span>
                        {{ $settings->availability_label }}
                    </div>
                @endif

                <p class="eyebrow reveal">UI/UX DESIGNER &amp; WEB DEVELOPER · {{ strtoupper($settings?->location ?: 'DHAKA, BD') }}</p>

                <h1 class="display split" data-split data-od-id="hero-headline" style="margin-top:var(--gap-lg)">
                    Med spa sites that book.
                    <span style="display:block;font-size:0.5em;line-height:1.16;letter-spacing:0;color:var(--muted);margin-top:0.14em">Systems that run the clinic.</span>
                </h1>

                <p class="lead reveal" style="margin-top:var(--gap-lg)">I design and build the full pipeline: Figma UI/UX → frontend → Laravel backend. Booking systems, client portals and admin dashboards, shipped end to end.</p>

                <div class="hero-cta reveal">
                    <a class="btn btn-primary btn-lg" href="{{ route('contact') }}" data-magnetic>Start a project</a>
                    <a class="btn btn-ghost btn-arrow btn-lg js-nav" href="{{ route('projects.index') }}">See the work</a>
                </div>
            </div>

            @php
                $heroImage = $settings?->heroImageUrl()
                    ?: (\App\Models\AboutPage::current()?->portraitUrl() ?: asset('assets/img/portrait.jpg'));
            @endphp

            <figure class="hero-figure reveal">
                <img src="{{ $heroImage }}" alt="Portrait of Md Naime, UI/UX designer and web developer"
                     width="896" height="1195" fetchpriority="high" decoding="async" data-parallax="0.05" style="object-position:50% 50%">
                <figcaption class="hero-caption">
                    <span class="dot" aria-hidden="true"></span>
                    <span class="meta">{{ $settings?->location ?: 'Dhaka' }} · working with clinics worldwide</span>
                </figcaption>
            </figure>
        </div>
        <div class="container">
            <div class="hero-scroll reveal">
                <span class="line" aria-hidden="true"></span>
                <span class="meta">Scroll to see selected projects</span>
            </div>
        </div>
    </section>

    <div class="marquee" aria-hidden="true">
        <div class="marquee-track">
            @foreach ($marquee as $item)
                <span class="marquee-item">{{ $item }}<span class="sep"></span></span>
            @endforeach
        </div>
    </div>

    <section class="section" data-od-id="work">
        <div class="container">
            <div class="row-between" style="margin-bottom:var(--gap-xl);flex-wrap:wrap;gap:var(--gap-md)">
                <div class="section-head">
                    <p class="eyebrow">SELECTED PROJECTS</p>
                    <h2>Recent projects, from Figma to Laravel.</h2>
                </div>
                <a class="link-underline js-nav" href="{{ route('projects.index') }}">View all projects</a>
            </div>

            @if ($projects->isEmpty())
                <p class="work-empty">Selected projects will appear here once the first case study is published.</p>
            @else
                <div class="work-grid work-grid--masonry">
                    @foreach ($projects as $project)
                        @include('site.partials.project-card', ['project' => $project, 'index' => $loop->index])
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="section" data-od-id="services">
        <div class="container">
            <div class="section-head" style="margin-bottom:var(--gap-xl)">
                <p class="eyebrow">WHAT I DO</p>
                <h2>Two ways I help clinics and businesses grow.</h2>
            </div>

            @if ($services->isEmpty())
                <p class="work-empty">Services are being updated. Start a project to talk through the scope.</p>
            @else
                <div class="service-block">
                    @foreach ($services as $service)
                        <article class="service-card reveal" data-od-id="service-{{ $service->slug }}">
                            <div class="row-between">
                                <span class="service-num">{{ str_pad((string) ($loop->index + 1), 2, '0', STR_PAD_LEFT) }} / SERVICE</span>
                                <span class="pill">{{ str($service->title)->before(' ') }}</span>
                            </div>
                            <h3>{{ $service->title }}</h3>
                            <p class="muted">{{ $service->short_description }}</p>
                            <ul class="service-features">
                                @foreach (array_slice($service->features ?? [], 0, 5) as $feature)
                                    <li>{{ $feature }}</li>
                                @endforeach
                            </ul>
                            <div class="spacer"></div>
                            <a class="link-underline js-nav" href="{{ route('services') }}#{{ $service->slug }}" style="margin-top:var(--gap-md)">Explore this service</a>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="section" data-od-id="process">
        <div class="container">
            <div class="section-head" style="margin-bottom:var(--gap-xl)">
                <p class="eyebrow">HOW IT WORKS</p>
                <h2>Figma first. Then WordPress or Laravel — whichever your business needs.</h2>
            </div>
            <div class="process-list">
                <div class="process-step">
                    <span class="n">01</span>
                    <h3>Discover</h3>
                    <p>A short strategy call to map your treatments, patients and the operations behind them.</p>
                    <p class="process-machine">→ strategy call</p>
                </div>
                <div class="process-step">
                    <span class="n">02</span>
                    <h3>Design</h3>
                    <p>Wireframes, UI and a component system in Figma — signed off before a line of code.</p>
                    <p class="process-machine">→ Figma UI/UX</p>
                </div>
                <div class="process-step">
                    <span class="n">03</span>
                    <h3>Build</h3>
                    <p>Elementor/WordPress for marketing sites, Laravel for booking systems, portals and dashboards.</p>
                    <p class="process-machine">→ WordPress · Laravel</p>
                </div>
                <div class="process-step">
                    <span class="n">04</span>
                    <h3>Launch &amp; iterate</h3>
                    <p>QA, performance, SEO, a clean handover and a calm first week after go-live.</p>
                    <p class="process-machine">→ deploy · measure</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section" data-od-id="stack">
        <div class="container">
            <div class="section-head" style="margin-bottom:var(--gap-xl)">
                <p class="eyebrow">TOOLKIT</p>
                <h2>The stack I ship with.</h2>
            </div>
            <div class="stack-grid">
                @foreach ($toolkit as $tool)
                    <div class="stack-cell">
                        <span class="name">{{ $tool['name'] ?? '' }}</span>
                        <span class="role">{{ $tool['role'] ?? '' }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section" data-od-id="testimonials">
        <div class="container">
            <div class="section-head" style="margin-bottom:var(--gap-xl)">
                <p class="eyebrow">CLIENTS</p>
                <h2>What it is like to work together.</h2>
            </div>
            @include('site.partials.testimonials', ['testimonials' => $testimonials])
        </div>
    </section>

    @include('site.partials.cta')
@endsection
