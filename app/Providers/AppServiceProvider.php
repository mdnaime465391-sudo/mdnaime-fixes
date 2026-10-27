<?php

namespace App\Providers;

use App\Models\Inquiry;
use App\Models\Service;
use App\Models\Setting;
use App\Support\ContentCache;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Paginator::defaultView('admin.partials.pagination');
        Paginator::defaultSimpleView('admin.partials.pagination');

        RateLimiter::for('inquiries', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        View::composer(['layouts.public', 'site.*', 'errors.*'], function ($view) {
            try {
                $view->with('settings', Setting::current());
            } catch (\Throwable $exception) {
                $view->with('settings', null);
            }
        });

        View::composer('layouts.admin', function ($view) {
            try {
                $view->with('navUnread', Inquiry::where('status', Inquiry::STATUS_NEW)->count());
            } catch (\Throwable $exception) {
                $view->with('navUnread', 0);
            }
        });

        View::composer('layouts.public', function ($view) {
            try {
                $view->with('footerServices', Cache::remember(
                    'public.footer.services',
                    ContentCache::SECONDS,
                    fn () => Service::published()->ordered()->get(['title', 'slug'])
                ));
            } catch (\Throwable $exception) {
                $view->with('footerServices', collect());
            }
        });
    }
}
