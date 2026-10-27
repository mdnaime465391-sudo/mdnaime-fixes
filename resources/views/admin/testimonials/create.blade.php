@extends('layouts.admin')

@section('title', 'New testimonial')

@section('content')
    <div class="page-head">
        <div>
            <p class="eyebrow"><a class="link" href="{{ route('admin.testimonials.index') }}">Testimonials</a> / New</p>
            <h1>A new quote.</h1>
            <p class="sub">Use the client's own words. Specific outcomes beat compliments.</p>
        </div>
        <div class="head-actions">
            <a class="abtn abtn-ghost" href="{{ route('admin.testimonials.index') }}">Cancel</a>
            <button class="abtn" type="submit" form="testimonial-form">Create testimonial</button>
        </div>
    </div>

    @include('admin.testimonials._form', ['testimonial' => $testimonial])
@endsection
