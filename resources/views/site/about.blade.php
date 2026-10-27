@extends('layouts.public')

@php
    $page = $page ?? null;
    $toolkit = $page?->toolkit ?: config('portfolio.about_defaults.toolkit');
    $principles = $page?->principles ?: [];
    $portrait = $page?->portraitUrl() ?: ($settings?->heroImageUrl() ?: asset('assets/img/portrait.jpg'));
@endphp

@section('content')
    <section class="section page-hero" data-od-id="page-hero">
        <div class="container grid-hero" style="align-items:center">
            <div>
                <p class="eyebrow reveal">{{ $page?->eyebrow ?: 'ABOUT' }}</p>
                <h1 class="split" data-split style="margin-top:var(--gap-lg)">{{ $page?->headline ?: 'I design the interface and build the system behind it.' }}</h1>
                <p class="lead reveal" style="margin-top:var(--gap-lg)">{{ $page?->intro ?: 'One person, the full pipeline: Figma UI/UX, frontend and the Laravel application behind it.' }}</p>

                <div class="hero-cta reveal">
                    <a class="btn btn-primary btn-lg" href="{{ route('contact') }}" data-magnetic>Start a project</a>
                    <a class="btn btn-ghost btn-arrow btn-lg" href="{{ route('projects.index') }}">See the projects</a>
                </div>
            </div>

            <figure class="hero-figure reveal">
                <img src="{{ $portrait }}" alt="Portrait of Md Naime" width="896" height="1195" loading="eager" decoding="async" style="object-position:50% 50%">
                <figcaption class="hero-caption">
                    <span class="dot" aria-hidden="true"></span>
                    <span class="meta">{{ $settings?->location ?: 'Dhaka, Bangladesh' }}</span>
                </figcaption>
            </figure>
        </div>
    </section>

    @if (($page?->show_story ?? true) && ($page?->story || $page?->story_heading))
        <section class="section" style="padding-top:0" data-od-id="story">
            <div class="container grid-lead">
                <div>
                    <p class="eyebrow">THE STORY</p>
                    <h2 style="margin-top:var(--gap-md)">{{ $page?->story_heading ?: 'A designer who ships.' }}</h2>
                </div>
                <div>
                    @if ($page?->story)
                        <div class="case-prose">{!! $page->story !!}</div>
                    @endif

                    <dl class="fact-list" style="margin-top:var(--gap-xl)">
                        <div class="fact"><span class="k">Based in</span><span class="v">{{ $settings?->location ?: 'Dhaka, Bangladesh' }}</span></div>
                        <div class="fact"><span class="k">Focus</span><span class="v">Med spas · Clinics · Custom apps</span></div>
                        <div class="fact"><span class="k">Pipeline</span><span class="v">Figma → frontend → Laravel</span></div>
                        @if ($page?->availability_label || $settings?->availability_label)
                            <div class="fact"><span class="k">Availability</span><span class="v">{{ $page?->availability_label ?: $settings->availability_label }}</span></div>
                        @endif
                    </dl>
                </div>
            </div>
        </section>
    @endif

    @if (($page?->show_principles ?? true) && ! empty($principles))
        <section class="section" data-od-id="principles">
            <div class="container">
                <div class="section-head" style="margin-bottom:var(--gap-xl)">
                    <p class="eyebrow">HOW I WORK</p>
                    <h2>Three things I do not compromise on.</h2>
                </div>

                <div class="principles-grid">
                    @foreach ($principles as $principle)
                        <article class="principle">
                            <span class="n">{{ str_pad((string) ($loop->index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <h3>{{ $principle['title'] ?? '' }}</h3>
                            @if (! empty($principle['body']))
                                <p>{{ $principle['body'] }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if (($page?->show_toolkit ?? true) && ! empty($toolkit))
        <section class="section" data-od-id="skills">
            <div class="container">
                <div class="section-head" style="margin-bottom:var(--gap-xl)">
                    <p class="eyebrow">SKILLS &amp; TOOLS</p>
                    <h2>The toolkit, used deeply.</h2>
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
    @endif

    @if (($page?->show_testimonials ?? true) && $testimonials->isNotEmpty())
        <section class="section" data-od-id="about-testimonial">
            <div class="container">
                <div class="section-head" style="margin-bottom:var(--gap-xl)">
                    <p class="eyebrow">CLIENTS</p>
                    <h2>What it is like to work together.</h2>
                </div>
                @include('site.partials.testimonials', ['testimonials' => $testimonials])
            </div>
        </section>
    @endif

    @if ($page?->show_cta ?? true)
        @include('site.partials.cta', [
            'heading' => 'If the fit sounds right, let’s talk.',
            'lead' => 'A short call is enough to know whether this is a website, a system, or a smaller first step.',
            'secondary' => true,
            'secondaryLabel' => 'See projects',
        ])
    @endif
@endsection
