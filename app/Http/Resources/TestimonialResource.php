<?php

namespace App\Http\Resources;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Testimonial */
class TestimonialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'role' => $this->role,
            'company' => $this->company,
            'quote' => $this->quote,
            'avatar' => $this->avatarUrl(),
            'is_published' => $this->is_published,
        ];
    }
}
