<?php

namespace App\Models;

use App\Support\ContentCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saved(fn () => ContentCache::services());
        static::deleted(fn () => ContentCache::services());
    }

    protected $fillable = [
        'title', 'slug', 'short_description', 'long_description',
        'icon', 'features', 'sort_order', 'is_published',
    ];

    protected $casts = [
        'features' => 'array',
        'is_published' => 'boolean',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }
}
