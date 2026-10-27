<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    protected function prepareForValidation(): void
    {
        $links = collect($this->input('social_links', []))
            ->filter(fn ($row) => filled($row['label'] ?? null) && filled($row['url'] ?? null))
            ->map(fn ($row) => ['label' => trim($row['label']), 'url' => trim($row['url'])])
            ->values()
            ->all();

        $this->merge(['social_links' => $links]);
    }

    public function rules(): array
    {
        return [
            'site_title' => ['required', 'string', 'max:191'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'availability_label' => ['nullable', 'string', 'max:80'],
            'location' => ['nullable', 'string', 'max:120'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:191'],
            'phone' => ['nullable', 'string', 'max:32'],
            'call_booking_url' => ['nullable', 'url', 'max:255'],
            'social_links' => ['nullable', 'array'],
            'social_links.*.label' => ['required', 'string', 'max:40'],
            'social_links.*.url' => ['required', 'url', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:png,webp,jpg,jpeg', 'max:4096'],
            'favicon' => ['nullable', 'file', 'mimes:png,ico,webp', 'max:512'],
            'og_image' => ['nullable', 'image', 'mimes:png,webp,jpg,jpeg', 'max:4096'],
            'hero_image' => ['nullable', 'image', 'mimes:png,webp,jpg,jpeg,avif', 'max:4096'],
        ];
    }
}
