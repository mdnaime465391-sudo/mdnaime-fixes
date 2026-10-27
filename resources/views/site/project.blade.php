@extends('layouts.public')

@php
    $gallery = $project->media->where('type', 'gallery');
    $figma = $project->media->where('type', 'figma');
    $elementor = $project->media->where('type', 'elementor');
    $screens = $gallery->isNotEmpty() ? $gallery : collect(range(1, 4));
    $hasElementor = $elementor->isNotEmpty()
        || collect($project->categories ?? [])->contains(fn ($category) => str_contains(strtolower($category), 'wordpress') || str_contains(strtolower($category), 'elementor'));
@endphp

@push('head')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'CreativeWork',
            'name' => $project->title,
            'headline' => $project->title,
            'description' => $project->summary ?: $project->challenge,
            'url' => route('projects.show', $project->slug),
            'image' => $project->coverUrl(),
            'datePublished' => $project->published_at?->toIso8601String(),
            'dateModified' => $project->updated_at?->toIso8601String(),
            'creator' => ['@type' => 'Person', 'name' => $settings?->site_title ?: 'Md Naime'],
            'keywords' => implode(', ', $project->categories ?? []),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
    </script>
@endpush

@section('content')
    <section class="section page-hero" data-od-id="case-hero" style="padding-bottom:clamp(28px,4vw,56px)">
        <div class="container">
            <p class="eyebrow reveal"><a class="link-underline" href="{{ route('projects.index') }}">Projects</a> · CASE STUDY</p>
            <h1 class="split" data-split style="margin-top:var(--gap-lg);max-width:16ch">{{ $project->title }}</h1>
            @if ($project->summary)
                <p class="lead reveal" style="margin-top:var(--gap-md);max-width:56ch">{{ $project->summary }}</p>
            @endif

            <div class="case-cover reveal-img">
                @if ($project->coverUrl())
                    <img src="{{ $project->coverUrl() }}" alt="{{ $project->title }} — project cover" width="1600" height="900" fetchpriority="high">
                @else
                    <x-project-visual :project="$project" />
                @endif
            </div>

            <dl class="case-meta">
                <div><dt class="k">Category</dt><dd class="v">{{ collect($project->categories ?? [])->take(2)->implode(' · ') ?: 'Selected work' }}</dd></div>
                <div><dt class="k">Year</dt><dd class="v">{{ $project->published_at?->format('Y') ?? $project->created_at?->format('Y') }}</dd></div>
                <div><dt class="k">Pipeline</dt><dd class="v">{{ $project->hasLivePreview() ? 'Figma · Frontend · Laravel' : 'Figma · Frontend' }}</dd></div>
                <div><dt class="k">Live app</dt><dd class="v">{{ $project->hasLivePreview() ? 'Yes — preview below' : 'Marketing site' }}</dd></div>
            </dl>
        </div>
    </section>

    <section class="section" style="padding-top:0" data-od-id="case-story">
        <div class="container">
            @if ($project->background)
                <div class="case-block">
                    <h3>Background</h3>
                    <div class="case-prose">{!! $project->background !!}</div>
                </div>
            @endif

            @if ($project->challenge)
                <div class="case-block">
                    <h3>The challenge</h3>
                    <div class="case-prose">{!! $project->challenge !!}</div>
                </div>
            @endif

            @if ($project->design_approach)
                <div class="case-block">
                    <h3>Design approach</h3>
                    <div class="case-prose">{!! $project->design_approach !!}</div>
                </div>
            @endif
        </div>
    </section>

    <section class="section section--tight" data-od-id="case-screens">
        <div class="container">
            <div class="case-section-head">
                <p class="eyebrow">KEY SCREENS</p>
                <h2 style="margin-top:12px">The interface, in the states that matter.</h2>
            </div>

            <div class="gallery">
                @foreach ($screens as $screen)
                    <figure class="figure">
                        <div class="screen-tile wide">
                            @if ($screen instanceof \App\Models\ProjectMedia)
                                <img src="{{ $screen->url() }}" alt="{{ $screen->caption ?: $project->title.' screen' }}" loading="lazy" decoding="async" width="1200" height="750">
                            @else
                                <x-project-visual :project="$project" :variant="$screen % 2 === 0 ? 'app' : null" />
                            @endif
                        </div>
                        @if ($screen instanceof \App\Models\ProjectMedia && $screen->caption)
                            <figcaption>{{ $screen->caption }}</figcaption>
                        @endif
                    </figure>
                @endforeach
            </div>
        </div>
    </section>

    @if ($project->hasLivePreview())
        <section class="section" data-od-id="case-live">
            <div class="container">
                <div class="case-section-head row-between" style="flex-wrap:wrap;gap:16px">
                    <div>
                        <p class="eyebrow">LIVE PREVIEW</p>
                        <h2 style="margin-top:12px">The working app, not a screenshot.</h2>
                    </div>
                    <a class="btn btn-secondary btn-arrow" href="{{ $project->live_preview_url }}" target="_blank" rel="noopener noreferrer">Open live app</a>
                </div>

                <div class="live-grid">
                    <div class="live-wrap">
                        <div class="live-bar">
                            <span class="live-dots" aria-hidden="true"><i></i><i></i><i></i></span>
                            <span class="live-url">{{ $project->live_preview_url }}</span>
                            <a class="meta" href="{{ $project->live_preview_url }}" target="_blank" rel="noopener noreferrer">Open ↗</a>
                        </div>
                        <div class="live-frame" data-live-frame data-timeout="{{ config('portfolio.live_preview.fallback_after_ms') }}">
                            <iframe src="{{ $project->live_preview_url }}" title="{{ $project->title }} live application preview"
                                    loading="lazy" referrerpolicy="no-referrer"></iframe>
                            <div class="live-fallback">
                                <div class="live-fallback-inner">
                                    <p class="kicker">PREVIEW BLOCKED</p>
                                    <h3>This app does not allow embedding.</h3>
                                    <p>That is a security setting on the app, not a broken preview. Open it in a new tab to try the full experience.</p>
                                    <a class="btn btn-primary" href="{{ $project->live_preview_url }}" target="_blank" rel="noopener noreferrer">Open live app</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <aside class="creds-card" aria-label="Demo access">
                        <div>
                            <p class="kicker">DEMO ACCESS</p>
                            <p class="muted" style="margin-top:8px;font-size:13.5px">Use these details to explore the app without touching client data.</p>
                        </div>

                        @if ($project->demo_credentials)
                            <div class="creds-row">
                                <span class="k">Credentials</span>
                                <span class="creds-code">{{ $project->demo_credentials }}</span>
                            </div>
                        @endif

                        <div class="creds-row">
                            <span class="k">Live URL</span>
                            <a class="v link-underline" href="{{ $project->live_preview_url }}" target="_blank" rel="noopener noreferrer" style="font-size:12px">Open ↗</a>
                        </div>

                        @if ($project->demo_notes)
                            <div class="note-box">{!! nl2br(e($project->demo_notes)) !!}</div>
                        @endif

                        <a class="btn btn-secondary btn-block" href="{{ $project->live_preview_url }}" target="_blank" rel="noopener noreferrer">Open the live app</a>
                    </aside>
                </div>

                <p class="help" style="margin-top:14px">Some apps block embedding for security. If the frame stays blank, use <strong>Open live app</strong>.</p>
            </div>
        </section>
    @endif

    @if (! empty($project->features))
        <section class="section section--tight" data-od-id="case-features">
            <div class="container">
                <div class="case-section-head">
                    <p class="eyebrow">WHAT IT DOES</p>
                    <h2 style="margin-top:12px">Built around the work, not the template.</h2>
                </div>
                <ul class="feature-list">
                    @foreach ($project->features as $feature)
                        <li><span class="n">{{ str_pad((string) ($loop->index + 1), 2, '0', STR_PAD_LEFT) }}</span><span>{{ $feature }}</span></li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    <section class="section section--tight" data-od-id="case-design-files">
        <div class="container">
            <div class="case-section-head">
                <p class="eyebrow">DESIGN FILES</p>
                <h2 style="margin-top:12px">From Figma frames to the shipped build.</h2>
            </div>

            <h3 class="case-subhead">Figma · UI/UX</h3>
            <div class="gallery">
                @if ($figma->isNotEmpty())
                    @foreach ($figma as $media)
                        <figure class="figure">
                            <div class="screen-tile wide"><img src="{{ $media->url() }}" alt="{{ $media->caption ?: $project->title.' Figma frame' }}" loading="lazy" width="1200" height="750"></div>
                            @if ($media->caption)<figcaption>{{ $media->caption }}</figcaption>@endif
                        </figure>
                    @endforeach
                @else
                    @for ($i = 0; $i < 2; $i++)
                        <figure class="figure">
                            <div class="screen-tile wide"><x-project-visual :project="$project" :variant="$i === 1 ? 'app' : null" /></div>
                        </figure>
                    @endfor
                @endif
            </div>

            @if ($hasElementor)
                <h3 class="case-subhead">Elementor / WordPress build</h3>
                <div class="gallery">
                    @if ($elementor->isNotEmpty())
                        @foreach ($elementor as $media)
                            <figure class="figure">
                                <div class="screen-tile wide"><img src="{{ $media->url() }}" alt="{{ $media->caption ?: $project->title.' Elementor build' }}" loading="lazy" width="1200" height="750"></div>
                                @if ($media->caption)<figcaption>{{ $media->caption }}</figcaption>@endif
                            </figure>
                        @endforeach
                    @else
                        @for ($i = 0; $i < 2; $i++)
                            <figure class="figure">
                                <div class="screen-tile wide"><x-project-visual :project="$project" /></div>
                            </figure>
                        @endfor
                    @endif
                </div>
            @endif
        </div>
    </section>

    @if ($project->solution || $project->result)
        <section class="section" data-od-id="case-outcome">
            <div class="container">
                <div class="case-block">
                    <h3>The outcome</h3>
                    <div class="case-prose">
                        <h2 style="margin-top:0">What we built, and what changed.</h2>
                        @if ($project->solution){!! $project->solution !!}@endif
                        @if ($project->result){!! $project->result !!}@endif
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section class="section section--tight">
        <div class="container">
            @if ($next)
                <a class="next-project" href="{{ route('projects.show', $next->slug) }}">
                    <span class="np-label">NEXT PROJECT</span>
                    <span class="np-title" style="display:block">{{ $next->title }}</span>
                </a>
            @else
                @include('site.partials.cta', [
                    'heading' => 'Have a project like this in mind?',
                    'lead' => 'Tell me where the clinic or the app is now, and what you want it to do next.',
                    'secondary' => false,
                ])
            @endif
        </div>
    </section>
@endsection
