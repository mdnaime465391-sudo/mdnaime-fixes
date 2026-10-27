@extends('layouts.admin')

@section('title', 'Inquiry from '.$inquiry->name)

@section('content')
    <div class="page-head">
        <div>
            <p class="eyebrow">
                <a class="link" href="{{ route('admin.inquiries.index') }}">Inquiries</a> / Detail
                <span class="badge badge--{{ $inquiry->status }}" style="margin-left:8px">{{ $inquiry->status }}</span>
            </p>
            <h1>{{ $inquiry->name }}</h1>
            <p class="sub">{{ $inquiry->created_at?->format('l, F j, Y · g:i A') }} · {{ $inquiry->created_at?->diffForHumans() }}</p>
        </div>
        <div class="head-actions">
            <a class="abtn abtn-ghost" href="mailto:{{ $inquiry->email }}?subject={{ rawurlencode('Re: your project inquiry — Md Naime') }}">Preview email</a>
            <a class="abtn abtn-primary" href="mailto:{{ $inquiry->email }}?subject={{ rawurlencode('Re: your project inquiry — Md Naime') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M4 6h16v12H4z"/><path d="m4 7 8 6 8-6"/></svg>
                Reply by email
            </a>
        </div>
    </div>

    <div class="dash-grid">
        <section class="card" aria-labelledby="message-heading">
            <div class="card-head">
                <div>
                    <h2 id="message-heading">The message</h2>
                    <p>Submitted through the contact form.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="quote-block">
                    <p class="inquiry-message">{{ $inquiry->message }}</p>
                </div>

                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:24px;padding-top:18px;border-top:1px solid var(--border)">
                    @if ($inquiry->service)
                        <span class="badge badge--accent">{{ $inquiry->service }}</span>
                    @endif
                    @if ($inquiry->business_type)
                        <span class="tag">{{ $inquiry->business_type }}</span>
                    @endif
                    @if ($inquiry->budget)
                        <span class="tag">{{ $inquiry->budget }}</span>
                    @endif
                </div>
            </div>
        </section>

        <div class="stack">
            <section class="card" aria-labelledby="contact-heading">
                <div class="card-head"><h2 id="contact-heading">Contact</h2></div>
                <div class="card-body">
                    <dl class="contact-list">
                        <div class="contact-row"><dt>Name</dt><dd>{{ $inquiry->name }}</dd></div>
                        <div class="contact-row"><dt>Email</dt><dd><a class="link" href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a></dd></div>
                        <div class="contact-row"><dt>Business</dt><dd>{{ $inquiry->business_type ?: '—' }}</dd></div>
                        <div class="contact-row"><dt>Service</dt><dd>{{ $inquiry->service ?: '—' }}</dd></div>
                        <div class="contact-row"><dt>Budget</dt><dd>{{ $inquiry->budget ?: '—' }}</dd></div>
                        <div class="contact-row"><dt>Received</dt><dd>{{ $inquiry->created_at?->format('M j, Y') }}</dd></div>
                    </dl>

                    @if ($inquiry->ip_address || $inquiry->user_agent)
                        <details style="margin-top:18px;padding-top:14px;border-top:1px solid var(--border)">
                            <summary class="faint small" style="cursor:pointer">Submission details</summary>
                            <dl class="contact-list" style="margin-top:12px">
                                <div class="contact-row"><dt>IP</dt><dd class="mono">{{ $inquiry->ip_address ?: '—' }}</dd></div>
                                <div class="contact-row"><dt>Agent</dt><dd class="small">{{ str($inquiry->user_agent)->limit(60) ?: '—' }}</dd></div>
                            </dl>
                        </details>
                    @endif
                </div>
            </section>

            <section class="card" aria-labelledby="workflow-heading">
                <div class="card-head"><h2 id="workflow-heading">Workflow</h2></div>
                <div class="card-body">
                    <p class="help" style="margin-bottom:12px">Opening an inquiry marks it read. Set it to replied once you have answered.</p>
                    <div class="status-actions">
                        @foreach (['read' => 'Mark read', 'replied' => 'Mark replied', 'new' => 'Mark unread'] as $status => $label)
                            <form method="POST" action="{{ route('admin.inquiries.status', $inquiry) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ $status }}">
                                <button class="abtn abtn-sm" type="submit">{{ $label }}</button>
                            </form>
                        @endforeach
                    </div>

                    <form method="POST" action="{{ route('admin.inquiries.destroy', $inquiry) }}" style="margin-top:18px;padding-top:14px;border-top:1px solid var(--border)"
                          data-confirm="Delete this inquiry? This cannot be undone.">
                        @csrf
                        @method('DELETE')
                        <button class="abtn abtn-ghost abtn-sm" type="submit" style="color:var(--bad)">Delete inquiry</button>
                    </form>
                </div>
            </section>
        </div>
    </div>
@endsection
