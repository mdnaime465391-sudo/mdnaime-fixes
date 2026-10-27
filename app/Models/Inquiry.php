<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    public const STATUS_NEW = 'new';

    public const STATUS_READ = 'read';

    public const STATUS_REPLIED = 'replied';

    public const UPDATED_AT = null; // inquiries are immutable once submitted

    protected $fillable = [
        'name', 'email', 'business_type', 'service', 'budget', 'message',
        'status', 'ip_address', 'user_agent', 'created_at',
    ];

    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderByDesc('created_at');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn (Builder $q) => $q->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('message', 'like', "%{$term}%");
        }));
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return $query->when(in_array($status, [self::STATUS_NEW, self::STATUS_READ, self::STATUS_REPLIED], true),
            fn (Builder $q) => $q->where('status', $status));
    }
}
