<?php

namespace App\Models;

use App\Support\ContentCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'categories', 'summary', 'cover_image', 'thumbnail',
        'accent_color', 'background', 'challenge', 'design_approach',
        'features', 'tech_stack', 'solution', 'result', 'live_preview_url',
        'demo_credentials', 'demo_notes', 'sort_order', 'is_published',
        'published_at',
    ];

    protected static function booted(): void
    {
        static::saved(fn (self $project) => ContentCache::project($project));
        static::deleted(fn (self $project) => ContentCache::project($project));
    }

    protected $casts = [
        'categories' => 'array',
        'features' => 'array',
        'tech_stack' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function media(): HasMany
    {
        return $this->hasMany(ProjectMedia::class)->orderBy('sort_order');
    }

    public function gallery(): HasMany
    {
        return $this->media()->where('type', 'gallery');
    }

    public function figma(): HasMany
    {
        return $this->media()->where('type', 'figma');
    }

    public function elementor(): HasMany
    {
        return $this->media()->where('type', 'elementor');
    }

    /** Only published rows for the public site. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /** Ordered exactly how the admin ranked them. */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }

    /** Full payload for a case-study page in one round-trip (no N+1). */
    public function scopeWithMedia(Builder $query): Builder
    {
        return $query->with(['media' => fn ($q) => $q->orderBy('sort_order')]);
    }

    public function hasLivePreview(): bool
    {
        return filled($this->live_preview_url);
    }

    public function coverUrl(): ?string
    {
        return $this->cover_image ? asset('storage/'.$this->cover_image) : null;
    }

    public function thumbUrl(): ?string
    {
        $path = $this->thumbnail ?: $this->cover_image;

        if (! $path) {
            return null;
        }

        if ($this->thumbnail) {
            return asset('storage/'.$this->thumbnail);
        }

        return asset('storage/'.preg_replace('/\.webp$/', '-thumb.webp', $this->cover_image));
    }

    public function accent(): string
    {
        if ($this->accent_color) {
            return $this->accent_color;
        }

        $palettes = [
            'oklch(80% 0.125 76)',
            'oklch(78% 0.085 22)',
            'oklch(76% 0.075 205)',
            'oklch(77% 0.070 150)',
            'oklch(76% 0.080 305)',
        ];

        return $palettes[$this->id % count($palettes)];
    }

    /** Deterministic mock-UI variant for projects without photography. */
    public function visualVariant(): string
    {
        $app = collect($this->categories ?? [])->contains(
            fn ($c) => str_contains(strtolower($c), 'app') || str_contains(strtolower($c), 'dashboard') || str_contains(strtolower($c), 'booking')
        );

        return $app ? 'app' : ['site', 'editorial', 'grid'][$this->id % 3];
    }
}
