<?php

namespace App\Http\Resources;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Project */
class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $media = $this->relationLoaded('media')
            ? ProjectMediaResource::collection($this->media)->resolve()
            : [];

        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'categories' => $this->categories ?? [],
            'summary' => $this->summary,
            'cover_image' => $this->coverUrl(),
            'thumbnail' => $this->thumbUrl(),
            'accent_color' => $this->accent_color,
            'background' => $this->background,
            'challenge' => $this->challenge,
            'design_approach' => $this->design_approach,
            'features' => $this->features ?? [],
            'tech_stack' => $this->tech_stack ?? [],
            'solution' => $this->solution,
            'result' => $this->result,
            'live_preview_url' => $this->live_preview_url,
            'demo_credentials' => $this->demo_credentials,
            'demo_notes' => $this->demo_notes,
            'is_published' => $this->is_published,
            'published_at' => $this->published_at?->toIso8601String(),
            'media' => [
                'gallery' => array_values(array_filter($media, fn ($item) => $item['type'] === 'gallery')),
                'figma' => array_values(array_filter($media, fn ($item) => $item['type'] === 'figma')),
                'elementor' => array_values(array_filter($media, fn ($item) => $item['type'] === 'elementor')),
            ],
            'url' => route('projects.show', $this->slug),
        ];
    }
}
