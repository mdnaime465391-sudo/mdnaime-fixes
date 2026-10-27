@extends('layouts.public')

@section('content')
    <section class="section page-hero" data-od-id="page-hero" style="position:relative">
        <div class="hero-glow" aria-hidden="true" style="top:-26%;right:-6%;width:46vw;height:46vw;max-width:640px;max-height:640px"></div>
        <div class="container grid-hero" style="align-items:center">
            <div>
                <p class="eyebrow reveal">PROJECTS · {{ $projects->min('published_at')?->format('Y') ?? '2024' }}—{{ now()->year }}</p>
                <h1 class="split" data-split style="margin-top:var(--gap-lg)">Selected projects across med spas, WordPress and Laravel.</h1>
                <p class="lead reveal" style="margin-top:var(--gap-lg)">A growing set of projects built across the full pipeline — interface design, marketing websites and custom applications. Filter by what you need.</p>
            </div>

            <div class="fact-list reveal" aria-label="Projects at a glance">
                <div class="fact"><span class="k">Projects</span><span class="v">{{ str_pad((string) $projects->count(), 2, '0', STR_PAD_LEFT) }}</span></div>
                <div class="fact"><span class="k">Disciplines</span><span class="v">{{ str_pad((string) $categories->count(), 2, '0', STR_PAD_LEFT) }}</span></div>
                <div class="fact"><span class="k">Live previews</span><span class="v">{{ str_pad((string) $liveCount, 2, '0', STR_PAD_LEFT) }}</span></div>
                <div class="fact"><span class="k">Based in</span><span class="v">{{ $settings?->location ?: 'Dhaka, BD' }}</span></div>
            </div>
        </div>
    </section>

    <section class="section" style="padding-top:0" data-od-id="work-index">
        <div class="container">
            <div class="filter-bar" data-filter-bar role="group" aria-label="Filter projects by category">
                <button class="chip is-active" type="button" data-filter="all" aria-pressed="true">All <span class="count">{{ $projects->count() }}</span></button>
                @foreach ($categories as $category => $count)
                    <button class="chip" type="button" data-filter="{{ $category }}" aria-pressed="false">{{ $category }} <span class="count">{{ $count }}</span></button>
                @endforeach
            </div>

            <p class="work-empty" data-work-empty hidden>No projects in this category yet.</p>

            @if ($projects->isEmpty())
                <p class="work-empty">No projects are published yet. Start a project and this page will fill up.</p>
            @else
                <div class="work-grid work-grid--masonry" data-work-grid style="margin-top:var(--gap-xl)">
                    @foreach ($projects as $project)
                        @include('site.partials.project-card', ['project' => $project, 'index' => $loop->index])
                    @endforeach
                </div>

                <div class="work-more" data-load-more-wrap>
                    <button class="btn btn-secondary btn-lg" type="button" data-load-more aria-expanded="false">Show more projects</button>
                </div>
            @endif
        </div>
    </section>

    @include('site.partials.cta', [
        'heading' => 'Your project could be the next one here.',
        'lead' => 'Bring the clinic, the app idea or the messy internal process. We will scope it on a short call.',
        'secondary' => false,
    ])
@endsection
