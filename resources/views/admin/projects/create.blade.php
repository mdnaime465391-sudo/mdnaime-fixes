@extends('layouts.admin')

@section('title', 'New project')

@section('content')
    <div class="page-head">
        <div>
            <p class="eyebrow"><a class="link" href="{{ route('admin.projects.index') }}">Projects</a> / New</p>
            <h1>A new case study.</h1>
            <p class="sub">Save the foundation first — the gallery manager appears on the edit screen.</p>
        </div>
        <div class="head-actions">
            <a class="abtn abtn-ghost" href="{{ route('admin.projects.index') }}">Cancel</a>
            <button class="abtn" type="submit" form="project-form">Create project</button>
        </div>
    </div>

    @include('admin.projects._form', ['project' => $project, 'categories' => $categories])

    <p class="help" style="margin-top:16px">After saving you can upload gallery, Figma and Elementor images, drag them into order and add captions.</p>
@endsection
