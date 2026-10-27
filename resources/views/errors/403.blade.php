@extends('layouts.public')

@section('content')
    <section class="error-page">
        <div class="container">
            <p class="eyebrow">403 — RESTRICTED</p>
            <h1>Private room.</h1>
            <p>This area is for the site administrator. If you were looking for the work, it is one click away.</p>
            <a class="btn btn-primary btn-lg" href="{{ route('home') }}">Back to the site</a>
        </div>
    </section>
@endsection
