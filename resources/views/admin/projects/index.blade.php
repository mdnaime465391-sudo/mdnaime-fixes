@extends('layouts.admin')

@section('title', 'Projects')

@section('content')
    <div class="page-head">
        <div>
            <p class="eyebrow">Portfolio</p>
            <h1>Projects.</h1>
            <p class="sub">Drafts stay invisible to the public site. Publish when the case study and media are ready.</p>
        </div>
        <div class="head-actions">
            <a class="abtn abtn-primary" href="{{ route('admin.projects.create') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                New project
            </a>
        </div>
    </div>

    <section class="card">
        <div class="inbox-tabs" role="group" aria-label="Filter by status">
            <a class="inbox-tab {{ empty($filters['status']) ? 'is-active' : '' }}" href="{{ route('admin.projects.index', array_filter(['q' => $filters['q'] ?? null])) }}">All <b>{{ \App\Models\Project::count() }}</b></a>
            <a class="inbox-tab {{ ($filters['status'] ?? '') === 'published' ? 'is-active' : '' }}" href="{{ route('admin.projects.index', array_filter(['status' => 'published', 'q' => $filters['q'] ?? null])) }}">Published <b>{{ \App\Models\Project::published()->count() }}</b></a>
            <a class="inbox-tab {{ ($filters['status'] ?? '') === 'draft' ? 'is-active' : '' }}" href="{{ route('admin.projects.index', array_filter(['status' => 'draft', 'q' => $filters['q'] ?? null])) }}">Drafts <b>{{ \App\Models\Project::where('is_published', false)->count() }}</b></a>
        </div>

        <form class="toolbar" method="GET" action="{{ route('admin.projects.index') }}" role="search">
            @if (! empty($filters['status']))
                <input type="hidden" name="status" value="{{ $filters['status'] }}">
            @endif
            <label class="search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="11" cy="11" r="6.5"/><path d="m16 16 4.5 4.5"/></svg>
                <span class="sr-only">Search projects</span>
                <input class="input" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search title or slug">
            </label>
            <button class="abtn abtn-sm" type="submit">Filter</button>
            @if (! empty($filters['q']))
                <a class="abtn abtn-ghost abtn-sm" href="{{ route('admin.projects.index') }}">Clear</a>
            @endif
        </form>

        @if ($projects->isEmpty())
            <div class="table-empty">
                <h3>No projects match.</h3>
                <p>Try a different search, or start a new case study.</p>
                <a class="abtn abtn-primary" href="{{ route('admin.projects.create') }}">New project</a>
            </div>
        @else
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Categories</th>
                            <th>Media</th>
                            <th>Status</th>
                            <th>Updated</th>
                            <th style="text-align:right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($projects as $project)
                            <tr>
                                <td>
                                    <div class="cell-main">
                                        @if ($project->thumbUrl())
                                            <img class="thumb" src="{{ $project->thumbUrl() }}" alt="" loading="lazy" width="58" height="44">
                                        @else
                                            <span class="thumb" style="display:grid;place-items:center;color:var(--faint);font-family:var(--font-mono);font-size:8px">NO IMG</span>
                                        @endif
                                        <span style="min-width:0">
                                            <a class="cell-title" href="{{ route('admin.projects.edit', $project) }}">{{ $project->title }}</a>
                                            <span class="cell-sub">/{{ $project->slug }}</span>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="tag-list">
                                        @foreach (array_slice($project->categories ?? [], 0, 2) as $category)
                                            <span class="tag">{{ $category }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="muted small">{{ $project->media_count }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.projects.publish', $project) }}" data-autosubmit>
                                        @csrf
                                        @method('PATCH')
                                        <label class="switch" title="{{ $project->is_published ? 'Unpublish' : 'Publish' }}">
                                            <input type="checkbox" @checked($project->is_published) aria-label="Publish {{ $project->title }}">
                                            <span class="switch-track" aria-hidden="true"></span>
                                            <span class="switch-label">{{ $project->is_published ? 'Live' : 'Draft' }}</span>
                                        </label>
                                    </form>
                                </td>
                                <td class="muted small">{{ $project->updated_at?->format('M j, Y') }}</td>
                                <td>
                                    <div class="row-actions">
                                        @if ($project->is_published)
                                            <a class="abtn abtn-ghost abtn-sm" href="{{ route('projects.show', $project->slug) }}" target="_blank" rel="noopener">View</a>
                                        @endif
                                        <a class="abtn abtn-sm" href="{{ route('admin.projects.edit', $project) }}">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $projects->links() }}
        @endif
    </section>
@endsection
