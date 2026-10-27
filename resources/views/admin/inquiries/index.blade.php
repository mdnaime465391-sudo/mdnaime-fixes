@extends('layouts.admin')

@section('title', 'Inquiries')

@section('content')
    <div class="page-head">
        <div>
            <p class="eyebrow">Inbox</p>
            <h1>Inquiries.</h1>
            <p class="sub">Every contact-form submission. Replies go by email — nothing is sent from the admin automatically.</p>
        </div>
        <div class="head-actions">
            <a class="abtn" href="{{ route('admin.inquiries.export', array_filter(['q' => $filters['q'] ?? null, 'status' => $filters['status'] ?? null])) }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M12 4v11M7.5 10.5 12 15l4.5-4.5M5 19h14"/></svg>
                Export CSV
            </a>
        </div>
    </div>

    <section class="card">
        <div class="inbox-tabs" role="group" aria-label="Filter by status">
            <a class="inbox-tab {{ empty($filters['status']) ? 'is-active' : '' }}" href="{{ route('admin.inquiries.index', array_filter(['q' => $filters['q'] ?? null])) }}">All <b>{{ $counts['all'] }}</b></a>
            <a class="inbox-tab {{ ($filters['status'] ?? '') === 'new' ? 'is-active' : '' }}" href="{{ route('admin.inquiries.index', array_filter(['status' => 'new', 'q' => $filters['q'] ?? null])) }}">New <b>{{ $counts['new'] }}</b></a>
            <a class="inbox-tab {{ ($filters['status'] ?? '') === 'read' ? 'is-active' : '' }}" href="{{ route('admin.inquiries.index', array_filter(['status' => 'read', 'q' => $filters['q'] ?? null])) }}">Read <b>{{ $counts['read'] }}</b></a>
            <a class="inbox-tab {{ ($filters['status'] ?? '') === 'replied' ? 'is-active' : '' }}" href="{{ route('admin.inquiries.index', array_filter(['status' => 'replied', 'q' => $filters['q'] ?? null])) }}">Replied <b>{{ $counts['replied'] }}</b></a>
        </div>

        <form class="toolbar" method="GET" action="{{ route('admin.inquiries.index') }}" role="search">
            @if (! empty($filters['status']))
                <input type="hidden" name="status" value="{{ $filters['status'] }}">
            @endif
            <label class="search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="11" cy="11" r="6.5"/><path d="m16 16 4.5 4.5"/></svg>
                <span class="sr-only">Search inquiries</span>
                <input class="input" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search name, email or message">
            </label>
            <button class="abtn abtn-sm" type="submit">Search</button>
            @if (! empty($filters['q']))
                <a class="abtn abtn-ghost abtn-sm" href="{{ route('admin.inquiries.index', array_filter(['status' => $filters['status'] ?? null])) }}">Clear</a>
            @endif
        </form>

        @if ($inquiries->isEmpty())
            <div class="table-empty">
                <h3>Nothing here yet.</h3>
                <p>{{ ! empty($filters['q']) || ! empty($filters['status']) ? 'No inquiries match this view.' : 'The first contact-form submission will appear here.' }}</p>
            </div>
        @else
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr><th>From</th><th>Interested in</th><th>Budget</th><th>Status</th><th>Received</th><th style="text-align:right">Open</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($inquiries as $inquiry)
                            <tr>
                                <td>
                                    <a class="cell-title" href="{{ route('admin.inquiries.show', $inquiry) }}">{{ $inquiry->name }}</a>
                                    <span class="cell-sub">{{ $inquiry->email }}</span>
                                </td>
                                <td>
                                    <span>{{ $inquiry->service ?: 'Not specified' }}</span>
                                    <span class="cell-sub">{{ $inquiry->business_type ?: 'Business type not given' }}</span>
                                </td>
                                <td class="muted small">{{ $inquiry->budget ?: '—' }}</td>
                                <td><span class="badge badge--{{ $inquiry->status }}">{{ $inquiry->status }}</span></td>
                                <td class="muted small">
                                    {{ $inquiry->created_at?->format('M j, Y') }}
                                    <span class="cell-sub">{{ $inquiry->created_at?->diffForHumans() }}</span>
                                </td>
                                <td>
                                    <div class="row-actions">
                                        <a class="abtn abtn-sm" href="{{ route('admin.inquiries.show', $inquiry) }}">Open</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $inquiries->links() }}
        @endif
    </section>
@endsection
