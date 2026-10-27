<?php

namespace App\Models;

use App\Support\ContentCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AboutPage extends Model
{
    protected $fillable = [
        'eyebrow', 'headline', 'intro', 'portrait_path', 'story_heading', 'story',
        'principles', 'toolkit', 'availability_label', 'show_story',
        'show_principles', 'show_toolkit', 'show_testimonials', 'show_cta',
        'seo_title', 'seo_description',
    ];

    protected $casts = [
        'principles' => 'array',
        'toolkit' => 'array',
        'show_story' => 'boolean',
        'show_principles' => 'boolean',
        'show_toolkit' => 'boolean',
        'show_testimonials' => 'boolean',
        'show_cta' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => ContentCache::about());
        static::deleted(fn () => ContentCache::about());
    }

    /** Singleton content row, cached for public rendering. */
    public static function current(): ?self
    {
        return Cache::remember('about.page', 3600, fn () => self::query()->first());
    }

    public function portraitUrl(): ?string
    {
        return $this->portrait_path ? asset('storage/'.$this->portrait_path) : null;
    }
}
