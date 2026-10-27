@extends('layouts.admin')

@section('title', 'Services')

@section('content')
    <div class="page-head">
        <div>
            <p class="eyebrow">Offerings</p>
            <h1>Services.</h1>
            <p class="sub">The two offers on the homepage and Services page. Unpublished services disappear from both.</p>
        </div>
        <div class="head-actions">
            <a class="abtn abtn-primary" href="{{ route('admin.services.create') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                New service
            </a>
        </div>
    </div>

    <section class="card">
        @if ($services->isEmpty())
            <div class="table-empty">
                <h3>No services yet.</h3>
                <p>Add the med spa website offer and the Laravel app offer.</p>
                <a class="abtn abtn-primary" href="{{ route('admin.services.create') }}">New service</a>
            </div>
        @else
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr><th>Service</th><th>Features</th><th>Order</th><th>Status</th><th style="text-align:right">Actions</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($services as $service)
                            <tr>
                                <td>
                                    <a class="cell-title" href="{{ route('admin.services.edit', $service) }}">{{ $service->title }}</a>
                                    <span class="cell-sub">{{ $service->short_description }}</span>
                                </td>
                                <td class="muted small">{{ count($service->features ?? []) }} listed</td>
                                <td class="mono small faint">{{ str_pad((string) $service->sort_order, 2, '0', STR_PAD_LEFT) }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.services.publish', $service) }}" data-autosubmit>
                                        @csrf
                                        @method('PATCH')
                                        <label class="switch" title="{{ $service->is_published ? 'Unpublish' : 'Publish' }}">
                                            <input type="checkbox" @checked($service->is_published) aria-label="Publish {{ $service->title }}">
                                            <span class="switch-track" aria-hidden="true"></span>
                                            <span class="switch-label">{{ $service->is_published ? 'Live' : 'Draft' }}</span>
                                        </label>
                                    </form>
                                </td>
                                <td>
                                    <div class="row-actions">
                                        <a class="abtn abtn-sm" href="{{ route('admin.services.edit', $service) }}">Edit</a>
                                        <form method="POST" action="{{ route('admin.services.destroy', $service) }}" data-confirm="Delete “{{ $service->title }}”? This cannot be undone.">
                                            @csrf
                                            @method('DELETE')
                                            <button class="abtn abtn-ghost abtn-sm" type="submit" style="color:var(--bad)">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
