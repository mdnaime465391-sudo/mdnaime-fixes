@props(['project', 'variant' => null])

@php
    $variant = $variant ?: $project->visualVariant();
    $isApp = $variant === 'app';
    $accent = $project->accent();
    $category = collect($project->categories ?? [])->first() ?: 'Selected work';
    $label = str($project->title)->limit(26);
@endphp

<div class="mock {{ $isApp ? 'mock--app' : '' }}"
     style="--m-accent: {{ $accent }}; --m-bg: var(--surface); --m-line: var(--border); --m-fg: var(--fg); --m-btn-ink: var(--bg);"
     aria-hidden="true">
    <div class="mock-bar">
        <span class="d"></span><span class="d"></span><span class="d"></span>
        <span class="url">{{ $project->slug }}.mdnaime.design</span>
    </div>

    @if ($isApp)
        <div class="mock-body">
            <div class="mock-side">
                <i></i><i></i><i></i><i></i><i></i>
            </div>
            <div class="mock-main">
                <span></span><span></span><span></span><span></span><span></span><span></span>
            </div>
        </div>
    @else
        <div class="mock-body">
            <div class="mock-nav">
                <span class="brand-sm">{{ $label }}</span>
                <span class="links"><i></i><i></i><i></i></span>
            </div>
            <div class="mock-hero">
                <span class="m-eyebrow">{{ $category }}</span>
                <span class="m-title">{{ $project->summary ? str($project->summary)->limit(58) : $project->title }}</span>
                <span class="m-btn">Book a consultation</span>
            </div>
            <div class="mock-cards"><span></span><span></span><span></span></div>
        </div>
    @endif
</div>
