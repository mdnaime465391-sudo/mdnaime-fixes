@extends('layouts.admin')

@section('title', 'Edit testimonial')

@section('content')
    <div class="page-head">
        <div>
            <p class="eyebrow">
                <a class="link" href="{{ route('admin.testimonials.index') }}">Testimonials</a> / Edit
                <span class="badge badge--{{ $testimonial->is_published ? 'live' : 'draft' }}" style="margin-left:8px">{{ $testimonial->is_published ? 'Live' : 'Draft' }}</span>
            </p>
            <h1>{{ $testimonial->name }}</h1>
            <p class="sub">Last saved {{ $testimonial->updated_at?->diffForHumans() }}.</p>
        </div>
        <div class="head-actions">
            <button class="abtn" type="submit" form="testimonial-form">Save changes</button>
        </div>
    </div>

    @include('admin.testimonials._form', ['testimonial' => $testimonial])
@endsection
