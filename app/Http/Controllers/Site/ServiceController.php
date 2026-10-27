<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Support\ContentCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $services = Cache::remember(ContentCache::SERVICES, ContentCache::SECONDS, function () {
            return Service::published()->ordered()->get();
        });

        return view('site.services', [
            'services' => $services,
            'title' => 'Services',
            'description' => 'Premium med spa website design and custom Laravel booking systems, client portals and admin dashboards — Figma to production.',
        ]);
    }
}
