<?php

return [
    'admin' => [
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],

    'seed' => [
        // Set SEED_DEMO=true in .env to seed the 9 sample projects in production.
        'demo' => env('SEED_DEMO', false),
    ],

    'uploads' => [
        'max_kb' => 4096,
        'cover_width' => 1920,
        'thumb_width' => 640,
        'gallery_width' => 1920,
        'gallery_thumb_width' => 640,
        'avatar_width' => 400,
    ],

    'cache_seconds' => 600,

    'live_preview' => [
        'fallback_after_ms' => 7000,
    ],

    'about_defaults' => [
        'toolkit' => [
            ['name' => 'Figma', 'role' => 'UI/UX design'],
            ['name' => 'Affinity Studio', 'role' => 'Photo editing & banner design'],
            ['name' => 'Midjourney', 'role' => 'AI imagery'],
            ['name' => 'DaVinci Resolve', 'role' => 'Motion design'],
            ['name' => 'WordPress', 'role' => 'CMS & content'],
            ['name' => 'Elementor', 'role' => 'WordPress builder'],
            ['name' => 'Laravel', 'role' => 'Backend framework'],
            ['name' => 'MySQL', 'role' => 'Database'],
            ['name' => 'Tailwind CSS', 'role' => 'Styling system'],
            ['name' => 'Alpine.js', 'role' => 'Lightweight JS'],
            ['name' => 'Livewire', 'role' => 'Reactive Laravel UI'],
            ['name' => 'GSAP', 'role' => 'Motion'],
            ['name' => 'Stripe', 'role' => 'Payments'],
            ['name' => 'Vibe Coding', 'role' => 'AI-assisted build'],
            ['name' => 'Cursor', 'role' => 'AI code editor'],
            ['name' => 'ChatGPT', 'role' => 'AI assistant'],
            ['name' => 'Claude', 'role' => 'AI assistant'],
            ['name' => 'Perplexity', 'role' => 'AI research'],
            ['name' => 'Gemini', 'role' => 'AI assistant'],
            ['name' => 'Grok', 'role' => 'AI assistant'],
            ['name' => 'SEO + Schema', 'role' => 'Discoverability'],
        ],
    ],
];
