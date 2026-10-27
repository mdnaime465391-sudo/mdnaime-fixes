@php
    $section = match (true) {
        request()->routeIs('admin.projects.*') => 'Projects',
        request()->routeIs('admin.services.*') => 'Services',
        request()->routeIs('admin.testimonials.*') => 'Testimonials',
        request()->routeIs('admin.about.*') => 'About page',
        request()->routeIs('admin.inquiries.*') => 'Inquiries',
        request()->routeIs('admin.settings.*') => 'Settings',
        request()->routeIs('admin.account.*') => 'Account',
        default => 'Overview',
    };

    $action = match (true) {
        request()->routeIs('*.create') => 'New',
        request()->routeIs('*.edit') => 'Edit',
        request()->routeIs('*.show') => 'Detail',
        default => 'All',
    };
@endphp

<header class="topbar">
    <button class="icon-btn menu-btn" type="button" data-menu-btn aria-label="Open navigation" aria-expanded="false" aria-controls="admin-navigation">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
    </button>

    <div class="topbar-crumb">
        <span>Admin</span>
        <span aria-hidden="true">/</span>
        <b>{{ $section }}</b>
        @if ($action !== 'All')
            <span aria-hidden="true">/</span>
            <span>{{ $action }}</span>
        @endif
    </div>

    <div class="topbar-actions">
        <a class="view-site" href="{{ route('home') }}" target="_blank" rel="noopener">
            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M14 4h6v6M20 4l-8 8M10 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-4"/></svg>
            <span>View site</span>
        </a>
        <button class="icon-btn" type="button" data-theme-toggle aria-label="Switch theme" title="Switch theme">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M20 14.5A8.5 8.5 0 0 1 9.5 4a8.5 8.5 0 1 0 10.5 10.5Z"/></svg>
        </button>
    </div>
</header>
