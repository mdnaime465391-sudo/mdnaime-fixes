<?php

namespace App\Http\Resources;

use App\Models\ProjectMedia;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ProjectMedia */
class ProjectMediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'url' => $this->url(),
            'thumb' => $this->thumbUrl(),
            'caption' => $this->caption,
            'order' => $this->sort_order,
        ];
    }
}
