<?php

namespace App\Support;

use App\Models\Project;
use Illuminate\Support\Facades\Cache;

/**
 * Public page cache keys. The public site is read-heavy and changes rarely,
 * so every index payload is cached for ten minutes and busted explicitly
 * when the matching admin mutation lands.
 */
class ContentCache
{
    public const HOME = 'public.home';

    public const WORK = 'public.work';

    public const SERVICES = 'public.services';

    public const ABOUT = 'public.about';

    public const SECONDS = 600;

    public static function project(?Project $project = null): void
    {
        Cache::forget(self::HOME);
        Cache::forget(self::WORK);

        if ($project) {
            Cache::forget('public.project.'.$project->slug);
        }
    }

    public static function services(): void
    {
        Cache::forget(self::HOME);
        Cache::forget(self::SERVICES);
        Cache::forget('public.footer.services');
    }

    public static function testimonials(): void
    {
        Cache::forget(self::HOME);
        Cache::forget(self::ABOUT);
        Cache::forget(self::SERVICES);
    }

    public static function settings(): void
    {
        Cache::forget('settings.current');
        Cache::forget(self::HOME);
        Cache::forget(self::ABOUT);
    }

    public static function about(): void
    {
        Cache::forget(self::ABOUT);
        Cache::forget(self::HOME);
        Cache::forget('about.page');
    }

    public static function all(): void
    {
        foreach ([self::HOME, self::WORK, self::SERVICES, self::ABOUT] as $key) {
            Cache::forget($key);
        }

        Cache::forget('settings.current');
        Cache::forget('about.page');
        Cache::forget('public.footer.services');
    }
}
