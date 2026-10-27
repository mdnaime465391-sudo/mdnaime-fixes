<?php

namespace App\Models;

use App\Support\ContentCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saved(fn () => ContentCache::testimonials());
        static::deleted(fn () => ContentCache::testimonials());
    }

    protected $fillable = ['name', 'role', 'company', 'quote', 'avatar', 'sort_order', 'is_published'];

    protected $casts = ['is_published' => 'boolean'];

    public function avatarUrl(): ?string
    {
        return $this->avatar ? asset('storage/'.$this->avatar) : null;
    }

    public function initials(): string
    {
        return collect(explode(' ', $this->name))
            ->filter()
            ->take(2)
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
