@extends('layouts.public')

@section('content')
    <section class="section page-hero" data-od-id="page-hero">
        <div class="container">
            <p class="eyebrow reveal">SERVICES</p>
            <h1 class="split" data-split style="margin-top:var(--gap-lg)">Two services, one pipeline: design it, build it, ship it.</h1>
            <p class="lead reveal" style="margin-top:var(--gap-lg);max-width:56ch">Most studios hand off between design and development. Here it stays in one pair of hands — from the Figma file to the Laravel application.</p>
        </div>
    </section>

    @forelse ($services as $service)
        <section class="section" id="{{ $service->slug }}" data-od-id="service-page-{{ $service->slug }}">
            <div class="container">
                <div class="grid-lead">
                    <div>
                        <p class="eyebrow">SERVICE {{ str_pad((string) ($loop->index + 1), 2, '0', STR_PAD_LEFT) }}</p>
                        <h2 style="margin-top:var(--gap-md)">{{ $service->title }}</h2>
                        <p class="lead" style="margin-top:var(--gap-md)">{{ $service->short_description }}</p>

                        <div class="service-visual reveal-img" style="margin-top:var(--gap-lg)">
                            @if ($loop->first)
                                <img src="{{ asset('assets/img/medspa-suite.jpg') }}" alt="Treatment suite photographed for a premium med spa website" loading="lazy" decoding="async" width="1200" height="900">
                            @else
                                <img src="{{ asset('assets/img/studio-desk.jpg') }}" alt="Dark developer workspace with a web application open on screen" loading="lazy" decoding="async" width="1200" height="900">
                            @endif
                        </div>
                    </div>

                    <div>
                        @if ($service->long_description)
                            <div class="case-prose">{!! $service->long_description !!}</div>
                        @endif

                        @if (! empty($service->features))
                            <ul class="service-features" style="margin-top:var(--gap-xl)">
                                @foreach ($service->features as $feature)
                                    <li>{{ $feature }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <div class="hero-cta">
                            <a class="btn btn-secondary btn-arrow" href="{{ route('contact') }}">Start a project</a>
                            <a class="btn btn-ghost" href="{{ route('projects.index') }}">See related projects</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @empty
        <section class="section">
            <div class="container">
                <p class="work-empty">Services are being updated. Start a project to talk through the scope.</p>
            </div>
        </section>
    @endforelse

    <section class="section" data-od-id="engagements">
        <div class="container">
            <div class="section-head" style="margin-bottom:var(--gap-xl)">
                <p class="eyebrow">HOW AN ENGAGEMENT RUNS</p>
                <h2>A calm process, with no black boxes.</h2>
            </div>

            <div class="process-list">
                <div class="process-step">
                    <span class="n">01</span>
                    <h3>Scope</h3>
                    <p>A short call, then a written scope: pages, screens, integrations, timeline and a fixed quote.</p>
                    <p class="process-machine">→ fixed quote</p>
                </div>
                <div class="process-step">
                    <span class="n">02</span>
                    <h3>Design</h3>
                    <p>Figma wireframes, then the visual system — reviewed in the browser, not as flat images.</p>
                    <p class="process-machine">→ Figma sign-off</p>
                </div>
                <div class="process-step">
                    <span class="n">03</span>
                    <h3>Build</h3>
                    <p>WordPress/Elementor or Laravel, with the admin your team actually needs to keep it current.</p>
                    <p class="process-machine">→ staging builds</p>
                </div>
                <div class="process-step">
                    <span class="n">04</span>
                    <h3>Handover</h3>
                    <p>Launch checks, SEO, analytics, a walkthrough and documentation — then support if you want it.</p>
                    <p class="process-machine">→ live · documented</p>
                </div>
            </div>
        </div>
    </section>

    @include('site.partials.cta', [
        'heading' => 'Not sure which service fits?',
        'lead' => 'Describe the clinic or the bottleneck. I will tell you whether it is a website, an app, or neither.',
        'secondary' => true,
        'secondaryLabel' => 'See projects',
    ])
@endsection
