@extends('layouts.admin')

@section('title', 'Overview')

@section('content')
    <div class="page-head">
        <div>
            <p class="eyebrow">Studio overview</p>
            <h1>Good to see you, {{ str(auth()->user()->name)->before(' ') }}.</h1>
            <p class="sub">Everything the public site reads from — projects, services, the About page and the inbox.</p>
        </div>
        <div class="head-actions">
            <a class="abtn abtn-primary" href="{{ route('admin.projects.create') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                New project
            </a>
        </div>
    </div>

    <div class="stat-grid">
        <article class="stat">
            <div class="stat-label"><span>Projects</span><span>{{ $stats['published'] }} live</span></div>
            <div class="stat-value">{{ str_pad((string) $stats['projects'], 2, '0', STR_PAD_LEFT) }}</div>
            <p class="stat-foot"><a href="{{ route('admin.projects.index') }}">Manage the portfolio</a></p>
        </article>

        <article class="stat">
            <div class="stat-label"><span>New inquiries</span><span>{{ $stats['inquiries'] }} total</span></div>
            <div class="stat-value" @if ($stats['unread'] > 0) style="color:var(--accent-text)" @endif>{{ str_pad((string) $stats['unread'], 2, '0', STR_PAD_LEFT) }}</div>
            <p class="stat-foot"><a href="{{ route('admin.inquiries.index', ['status' => 'new']) }}">{{ $stats['unread'] > 0 ? 'Reply while it is warm' : 'Inbox is clear' }}</a></p>
        </article>

        <article class="stat">
            <div class="stat-label"><span>Services</span><span>Offerings</span></div>
            <div class="stat-value">{{ str_pad((string) $stats['services'], 2, '0', STR_PAD_LEFT) }}</div>
            <p class="stat-foot"><a href="{{ route('admin.services.index') }}">Edit the two offers</a></p>
        </article>

        <article class="stat">
            <div class="stat-label"><span>Testimonials</span><span>Social proof</span></div>
            <div class="stat-value">{{ str_pad((string) $stats['testimonials'], 2, '0', STR_PAD_LEFT) }}</div>
            <p class="stat-foot"><a href="{{ route('admin.testimonials.index') }}">Manage quotes</a></p>
        </article>
    </div>

    <div class="dash-grid">
        <section class="card" aria-labelledby="recent-inquiries">
            <div class="card-head">
                <div>
                    <h2 id="recent-inquiries">Recent inquiries</h2>
                    <p>Newest first. Opening one marks it as read.</p>
                </div>
                <a class="abtn abtn-ghost abtn-sm" href="{{ route('admin.inquiries.index') }}">Open inbox →</a>
            </div>

            @if ($recentInquiries->isEmpty())
                <div class="table-empty">
                    <h3>No inquiries yet.</h3>
                    <p>When someone sends the contact form, it lands here and in your inbox.</p>
                </div>
            @else
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr><th>From</th><th>Project</th><th>Status</th><th>Received</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($recentInquiries as $inquiry)
                                <tr>
                                    <td>
                                        <a class="cell-title" href="{{ route('admin.inquiries.show', $inquiry) }}">{{ $inquiry->name }}</a>
                                        <span class="cell-sub">{{ $inquiry->email }}</span>
                                    </td>
                                    <td>
                                        <span>{{ $inquiry->service ?: 'Not specified' }}</span>
                                        <span class="cell-sub">{{ $inquiry->budget ?: 'Budget open' }}</span>
                                    </td>
                                    <td><span class="badge badge--{{ $inquiry->status }}">{{ $inquiry->status }}</span></td>
                                    <td class="muted small">{{ $inquiry->created_at?->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <div class="stack">
            <section class="card" aria-labelledby="recent-projects">
                <div class="card-head">
                    <div>
                        <h2 id="recent-projects">Recently edited</h2>
                        <p>The last five project records.</p>
                    </div>
                </div>
                <div class="card-body">
                    @forelse ($recentProjects as $project)
                        <a href="{{ route('admin.projects.edit', $project) }}" style="display:flex;align-items:center;gap:12px;padding:9px 0;border-bottom:1px solid var(--border)">
                            @if ($project->thumbUrl())
                                <img class="thumb" src="{{ $project->thumbUrl() }}" alt="" loading="lazy" width="58" height="44">
                            @else
                                <span class="thumb" style="display:grid;place-items:center;color:var(--faint);font-family:var(--font-mono);font-size:9px">NO IMG</span>
                            @endif
                            <span style="min-width:0;flex:1">
                                <span class="cell-title">{{ $project->title }}</span>
                                <span class="cell-sub">{{ $project->media_count }} media · {{ $project->updated_at?->diffForHumans() }}</span>
                            </span>
                            <span class="badge badge--{{ $project->is_published ? 'live' : 'draft' }}">{{ $project->is_published ? 'Live' : 'Draft' }}</span>
                        </a>
                    @empty
                        <p class="faint small">No projects yet.</p>
                    @endforelse

                    <a class="abtn abtn-sm abtn-block" href="{{ route('admin.projects.index') }}" style="margin-top:16px">All projects</a>
                </div>
            </section>

            <section class="card" aria-labelledby="quick-actions">
                <div class="card-head">
                    <h2 id="quick-actions">Quick actions</h2>
                </div>
                <div class="card-body" style="display:grid;gap:8px">
                    <a class="abtn abtn-sm" href="{{ route('admin.about.edit') }}">Edit the About page</a>
                    <a class="abtn abtn-sm" href="{{ route('admin.settings.edit') }}">Update contact &amp; SEO</a>
                    <a class="abtn abtn-sm" href="{{ route('admin.inquiries.index', ['status' => 'replied']) }}">Replied inquiries</a>
                    <a class="abtn abtn-sm" href="{{ route('home') }}" target="_blank" rel="noopener">Open the live site ↗</a>
                </div>
            </section>
        </div>
    </div>
@endsection
