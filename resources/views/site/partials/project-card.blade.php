@php
    $index = $index ?? 0;
    $categories = $project->categories ?? [];
@endphp

<article class="work-card reveal" data-categories="{{ implode('|', $categories) }}" data-od-id="work-{{ $project->slug }}">
    <a class="work-media" href="{{ route('projects.show', $project->slug) }}" aria-label="Open case study: {{ $project->title }}">
        <div class="zoom">
            @if ($project->thumbUrl())
                <img src="{{ $project->thumbUrl() }}" alt="{{ $project->title }} — project cover" loading="lazy" decoding="async" width="640" height="440"
                     style="width:100%;height:100%;object-fit:cover">
            @else
                <x-project-visual :project="$project" />
            @endif
        </div>
    </a>
    <div class="work-head">
        <div>
            <h3 class="work-title"><a href="{{ route('projects.show', $project->slug) }}">{{ $project->title }}</a></h3>
            <div class="work-tags">
                @foreach (array_slice($categories, 0, 2) as $category)
                    <span class="pill">{{ $category }}</span>
                @endforeach
                @if ($project->hasLivePreview())
                    <span class="pill pill--accent">Live app</span>
                @endif
            </div>
        </div>
        <span class="work-index">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }} / {{ $project->published_at?->format('Y') ?? now()->year }}</span>
    </div>
</article>
