@extends('layouts.admin')

@section('title', $project->title)

@section('content')
    <div class="page-head">
        <div>
            <p class="eyebrow">
                <a class="link" href="{{ route('admin.projects.index') }}">Projects</a> / Edit
                <span class="badge badge--{{ $project->is_published ? 'live' : 'draft' }}" style="margin-left:8px">{{ $project->is_published ? 'Live' : 'Draft' }}</span>
            </p>
            <h1>{{ $project->title }}</h1>
            <p class="sub">Last saved {{ $project->updated_at?->diffForHumans() }}. Changes go live immediately once published.</p>
        </div>
        <div class="head-actions">
            @if ($project->is_published)
                <a class="abtn abtn-ghost" href="{{ route('projects.show', $project->slug) }}" target="_blank" rel="noopener">View live ↗</a>
            @endif
            <button class="abtn" type="submit" form="project-form">Save changes</button>
        </div>
    </div>

    @include('admin.projects._form', ['project' => $project, 'categories' => $categories])

    @include('admin.projects.media', ['project' => $project])

    <section class="card" style="margin-top:22px" aria-labelledby="danger-heading">
        <div class="card-head">
            <div>
                <h2 id="danger-heading">Delete project</h2>
                <p>Removes the case study, its media files and any link to it. This cannot be undone.</p>
            </div>
            <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" data-confirm="Delete “{{ $project->title }}” and all of its images? This cannot be undone.">
                @csrf
                @method('DELETE')
                <button class="abtn abtn-danger" type="submit">Delete project</button>
            </form>
        </div>
    </section>
@endsection
