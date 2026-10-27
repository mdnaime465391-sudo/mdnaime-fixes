<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\AboutPage;
use App\Models\Project;
use App\Models\Service;
use App\Models\Testimonial;
use App\Support\ContentCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $content = Cache::remember(ContentCache::HOME, ContentCache::SECONDS, function () {
            return [
                'projects' => Project::published()->withMedia()->ordered()->take(3)->get(),
                'services' => Service::published()->ordered()->get(),
                'testimonials' => Testimonial::published()->ordered()->get(),
                'toolkit' => AboutPage::current()?->toolkit ?: config('portfolio.about_defaults.toolkit'),
            ];
        });

        return view('site.home', $content + [
            'title' => null,
            'description' => null,
        ]);
    }
}
