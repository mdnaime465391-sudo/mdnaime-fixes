<?php

namespace App\Models;

use App\Support\ContentCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMedia extends Model
{
    public const TYPES = ['gallery', 'figma', 'elementor'];

    protected $fillable = ['project_id', 'type', 'path', 'caption', 'sort_order'];

    protected static function booted(): void
    {
        $forget = function (self $media) {
            if ($media->project) {
                ContentCache::project($media->project);
            }
        };

        static::saved($forget);
        static::deleted($forget);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function url(): string
    {
        return asset('storage/'.$this->path);
    }

    public function thumbUrl(): string
    {
        return asset('storage/'.preg_replace('/\.webp$/', '-thumb.webp', $this->path));
    }
}
