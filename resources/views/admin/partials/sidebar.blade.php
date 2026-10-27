@php
    $user = auth()->user();
    $unread = $navUnread ?? 0;
    $links = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => 'grid'],
        ['label' => 'Projects', 'route' => 'admin.projects.index', 'active' => 'admin.projects.*', 'icon' => 'layers'],
        ['label' => 'Services', 'route' => 'admin.services.index', 'active' => 'admin.services.*', 'icon' => 'spark'],
        ['label' => 'Testimonials', 'route' => 'admin.testimonials.index', 'active' => 'admin.testimonials.*', 'icon' => 'quote'],
        ['label' => 'About page', 'route' => 'admin.about.edit', 'active' => 'admin.about.*', 'icon' => 'user'],
        ['label' => 'Inquiries', 'route' => 'admin.inquiries.index', 'active' => 'admin.inquiries.*', 'icon' => 'mail', 'badge' => $unread],
        ['label' => 'Settings', 'route' => 'admin.settings.edit', 'active' => 'admin.settings.*', 'icon' => 'sliders'],
        ['label' => 'Change password', 'route' => 'admin.account.password.edit', 'active' => 'admin.account.*', 'icon' => 'lock'],
    ];
@endphp

<aside class="sidebar" id="admin-navigation" data-sidebar aria-label="Admin navigation">
    <a class="brand" href="{{ route('admin.dashboard') }}">
        <span class="brand-mark" aria-hidden="true">N</span>
        <span class="brand-copy">
            <strong>{{ $settings_title ?? 'Md Naime' }}</strong>
            <span>Studio admin</span>
        </span>
    </a>

    <nav class="nav" aria-label="Primary">
        <div class="nav-group">
            <span class="nav-label">Overview</span>
            @php $item = $links[0]; @endphp
            @include('admin.partials.nav-link', ['item' => $item])
        </div>

        <div class="nav-group">
            <span class="nav-label">Content</span>
            @foreach (array_slice($links, 1, 4) as $item)
                @include('admin.partials.nav-link', ['item' => $item])
            @endforeach
        </div>

        <div class="nav-group">
            <span class="nav-label">Inbox</span>
            @php $item = $links[5]; @endphp
            @include('admin.partials.nav-link', ['item' => $item])
        </div>

        <div class="nav-group">
            <span class="nav-label">System</span>
            @php $item = $links[6]; @endphp
            @include('admin.partials.nav-link', ['item' => $item])
            @php $item = $links[7]; @endphp
            @include('admin.partials.nav-link', ['item' => $item])
        </div>
    </nav>

    <div class="side-foot">
        <div class="user-chip">
            <span class="user-avatar" aria-hidden="true">{{ $user ? strtoupper(substr($user->name, 0, 2)) : 'MN' }}</span>
            <div class="user-meta">
                <strong>{{ $user->name ?? 'Administrator' }}</strong>
                <span>{{ $user->email ?? '' }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-btn" type="submit" aria-label="Sign out" title="Sign out">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M15 5h3a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-3M10 17l-5-5 5-5M5 12h11"/></svg>
                </button>
            </form>
        </div>
    </div>
</aside>
