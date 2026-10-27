@php $isActive = request()->routeIs($item['active']); @endphp
<a class="nav-link {{ $isActive ? 'is-active' : '' }}" href="{{ route($item['route']) }}" @if ($isActive) aria-current="page" @endif>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
        @switch($item['icon'])
            @case('grid')
                <rect x="3.5" y="3.5" width="7" height="7" rx="1.2"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.2"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.2"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.2"/>
                @break
            @case('layers')
                <path d="M12 3.5 21 8l-9 4.5L3 8l9-4.5Z"/><path d="m3 12 9 4.5L21 12"/><path d="m3 16 9 4.5L21 16"/>
                @break
            @case('spark')
                <path d="M12 3v4M12 17v4M3 12h4M17 12h4M5.6 5.6l2.8 2.8M15.6 15.6l2.8 2.8M18.4 5.6l-2.8 2.8M8.4 15.6l-2.8 2.8"/><circle cx="12" cy="12" r="2.6"/>
                @break
            @case('quote')
                <path d="M9 7H5.5A2.5 2.5 0 0 0 3 9.5V14a3 3 0 0 0 3 3h2V7ZM19 7h-3.5A2.5 2.5 0 0 0 13 9.5V14a3 3 0 0 0 3 3h2V7Z"/>
                @break
            @case('user')
                <circle cx="12" cy="8" r="3.4"/><path d="M5 20a7 7 0 0 1 14 0"/>
                @break
            @case('mail')
                <rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3.5 7 8.5 6 8.5-6"/>
                @break
            @case('sliders')
                <path d="M4 7h9M17 7h3M4 17h3M11 17h9"/><circle cx="15" cy="7" r="2"/><circle cx="9" cy="17" r="2"/>
                @break
            @case('lock')
                <rect x="5" y="10.5" width="14" height="9.5" rx="2"/><path d="M8.5 10.5V7.5a3.5 3.5 0 0 1 7 0v3"/>
                @break
        @endswitch
    </svg>
    <span>{{ $item['label'] }}</span>
    @if (! empty($item['badge']))
        <span class="nav-badge">{{ $item['badge'] }}</span>
    @endif
</a>
