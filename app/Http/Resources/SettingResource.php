<?php

namespace App\Http\Resources;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Setting */
class SettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'site_title' => $this->site_title,
            'tagline' => $this->tagline,
            'availability_label' => $this->availability_label,
            'location' => $this->location,
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'email' => $this->email,
            'phone' => $this->phone,
            'social_links' => $this->social_links ?? [],
            'call_booking_url' => $this->call_booking_url,
            'logo' => $this->logoUrl(),
            'favicon' => $this->faviconUrl(),
            'og_image' => $this->ogImageUrl(),
        ];
    }
}
