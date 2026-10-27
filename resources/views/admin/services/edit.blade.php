@extends('layouts.admin')

@section('title', $service->title)

@section('content')
    <div class="page-head">
        <div>
            <p class="eyebrow">
                <a class="link" href="{{ route('admin.services.index') }}">Services</a> / Edit
                <span class="badge badge--{{ $service->is_published ? 'live' : 'draft' }}" style="margin-left:8px">{{ $service->is_published ? 'Live' : 'Draft' }}</span>
            </p>
            <h1>{{ $service->title }}</h1>
            <p class="sub">Last saved {{ $service->updated_at?->diffForHumans() }}.</p>
        </div>
        <div class="head-actions">
            @if ($service->is_published)
                <a class="abtn abtn-ghost" href="{{ route('services') }}#{{ $service->slug }}" target="_blank" rel="noopener">View on site ↗</a>
            @endif
            <button class="abtn" type="submit" form="service-form">Save changes</button>
        </div>
    </div>

    @include('admin.services._form', ['service' => $service])
@endsection
