<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\AboutPage;
use App\Models\Testimonial;
use App\Support\ContentCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function index(): View
    {
        $data = Cache::remember(ContentCache::ABOUT, ContentCache::SECONDS, function () {
            $page = AboutPage::current();

            return [
                'page' => $page,
                'testimonials' => $page?->show_testimonials
                    ? Testimonial::published()->ordered()->get()
                    : collect(),
            ];
        });

        return view('site.about', $data + [
            'title' => 'About',
            'description' => 'Md Naime — UI/UX designer and Laravel developer in Dhaka, building premium clinic websites and the systems behind them.',
        ]);
    }
}
