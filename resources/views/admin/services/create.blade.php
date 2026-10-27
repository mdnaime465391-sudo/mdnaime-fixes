@extends('layouts.admin')

@section('title', 'New service')

@section('content')
    <div class="page-head">
        <div>
            <p class="eyebrow"><a class="link" href="{{ route('admin.services.index') }}">Services</a> / New</p>
            <h1>A new offer.</h1>
            <p class="sub">Write it the way a clinic owner would explain the outcome, not the process.</p>
        </div>
        <div class="head-actions">
            <a class="abtn abtn-ghost" href="{{ route('admin.services.index') }}">Cancel</a>
            <button class="abtn" type="submit" form="service-form">Create service</button>
        </div>
    </div>

    @include('admin.services._form', ['service' => $service])
@endsection
