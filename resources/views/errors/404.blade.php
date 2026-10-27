@extends('layouts.public')

@section('content')
    <section class="error-page">
        <div class="container">
            <p class="eyebrow">404 — NOT FOUND</p>
            <h1>Lost the page.</h1>
            <p>The link may be old, or the project may have moved. The work is still here.</p>
            <a class="btn btn-primary btn-lg" href="{{ route('projects.index') }}">See the projects</a>
        </div>
    </section>
@endsection
