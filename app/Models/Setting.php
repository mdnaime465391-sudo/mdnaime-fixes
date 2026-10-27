<?php

namespace App\Models;

use App\Support\ContentCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'site_title', 'tagline', 'availability_label', 'location', 'seo_title',
        'seo_description', 'email', 'phone', 'social_links', 'call_booking_url',
        'logo_path', 'favicon_path', 'og_image_path', 'hero_image_path',
    ];

    protected $casts = ['social_links' => 'array'];

    protected static function booted(): void
    {
        static::saved(fn () => ContentCache::settings());
        static::deleted(fn () => ContentCache::settings());
    }

    public function logoUrl(): ?string
    {
        return $this->logo_path ? asset('storage/'.$this->logo_path) : null;
    }

    public function faviconUrl(): ?string
    {
        return $this->favicon_path ? asset('storage/'.$this->favicon_path) : null;
    }

    public function ogImageUrl(): ?string
    {
        return $this->og_image_path ? asset('storage/'.$this->og_image_path) : null;
    }

    public function heroImageUrl(): ?string
    {
        return $this->hero_image_path ? asset('storage/'.$this->hero_image_path) : null;
    }

    public function telHref(): ?string
    {
        return $this->phone ? 'tel:'.preg_replace('/[^0-9+]/', '', $this->phone) : null;
    }

    /**
     * Singleton access — settings are a single row (id 1), cached for
     * public-page performance and invalidated on save by the admin controller.
     */
    public static function current(): ?self
    {
        return Cache::remember('settings.current', 3600, fn () => self::query()->find(1));
    }
}
