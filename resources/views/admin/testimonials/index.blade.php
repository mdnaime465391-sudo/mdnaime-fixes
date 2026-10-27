@extends('layouts.admin')

@section('title', 'Testimonials')

@section('content')
    <div class="page-head">
        <div>
            <p class="eyebrow">Social proof</p>
            <h1>Testimonials.</h1>
            <p class="sub">Four quotes is plenty. Keep them specific, attributed and short enough to scan.</p>
        </div>
        <div class="head-actions">
            <a class="abtn abtn-primary" href="{{ route('admin.testimonials.create') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                New testimonial
            </a>
        </div>
    </div>

    @if ($testimonials->isEmpty())
        <section class="card">
            <div class="table-empty">
                <h3>No testimonials yet.</h3>
                <p>Add a quote from a client to show proof on the home, services and about pages.</p>
                <a class="abtn abtn-primary" href="{{ route('admin.testimonials.create') }}">New testimonial</a>
            </div>
        </section>
    @else
        <div class="grid-2">
            @foreach ($testimonials as $testimonial)
                <article class="card">
                    <div class="card-body">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px">
                            <div class="cell-main">
                                @if ($testimonial->avatarUrl())
                                    <img class="thumb" src="{{ $testimonial->avatarUrl() }}" alt="" loading="lazy" width="58" height="44" style="border-radius:50%;width:44px;height:44px">
                                @else
                                    <span class="user-avatar">{{ $testimonial->initials() }}</span>
                                @endif
                                <div>
                                    <span class="cell-title">{{ $testimonial->name }}</span>
                                    <span class="cell-sub">{{ collect([$testimonial->role, $testimonial->company])->filter()->implode(' · ') ?: 'Client' }}</span>
                                </div>
                            </div>
                            <span class="badge badge--{{ $testimonial->is_published ? 'live' : 'draft' }}">{{ $testimonial->is_published ? 'Live' : 'Draft' }}</span>
                        </div>

                        <blockquote class="quote-block" style="margin:16px 0 0;color:var(--muted);font-size:13px;line-height:1.7">
                            “{{ str($testimonial->quote)->limit(220) }}”
                        </blockquote>

                        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:18px;padding-top:14px;border-top:1px solid var(--border)">
                            <form method="POST" action="{{ route('admin.testimonials.publish', $testimonial) }}" data-autosubmit>
                                @csrf
                                @method('PATCH')
                                <label class="switch">
                                    <input type="checkbox" @checked($testimonial->is_published) aria-label="Publish {{ $testimonial->name }}">
                                    <span class="switch-track" aria-hidden="true"></span>
                                    <span class="switch-label">{{ $testimonial->is_published ? 'Live' : 'Draft' }}</span>
                                </label>
                            </form>
                            <div class="row-actions">
                                <a class="abtn abtn-sm" href="{{ route('admin.testimonials.edit', $testimonial) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.testimonials.destroy', $testimonial) }}" data-confirm="Delete the testimonial from {{ $testimonial->name }}?">
                                    @csrf
                                    @method('DELETE')
                                    <button class="abtn abtn-ghost abtn-sm" type="submit" style="color:var(--bad)">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endsection
