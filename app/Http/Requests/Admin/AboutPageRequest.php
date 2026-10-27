<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AboutPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    protected function prepareForValidation(): void
    {
        $principles = collect($this->input('principles', []))
            ->filter(fn ($row) => filled($row['title'] ?? null))
            ->map(fn ($row) => [
                'title' => trim($row['title']),
                'body' => trim($row['body'] ?? ''),
            ])
            ->values()
            ->all();

        $toolkit = collect($this->input('toolkit', []))
            ->filter(fn ($row) => filled($row['name'] ?? null))
            ->map(fn ($row) => [
                'name' => trim($row['name']),
                'role' => trim($row['role'] ?? ''),
            ])
            ->values()
            ->all();

        $this->merge([
            'principles' => $principles,
            'toolkit' => $toolkit,
            'show_story' => $this->boolean('show_story'),
            'show_principles' => $this->boolean('show_principles'),
            'show_toolkit' => $this->boolean('show_toolkit'),
            'show_testimonials' => $this->boolean('show_testimonials'),
            'show_cta' => $this->boolean('show_cta'),
        ]);
    }

    public function rules(): array
    {
        return [
            'eyebrow' => ['required', 'string', 'max:80'],
            'headline' => ['required', 'string', 'max:191'],
            'intro' => ['nullable', 'string', 'max:500'],
            'story_heading' => ['nullable', 'string', 'max:191'],
            'story' => ['nullable', 'string'],
            'principles' => ['nullable', 'array'],
            'principles.*.title' => ['required', 'string', 'max:120'],
            'principles.*.body' => ['nullable', 'string', 'max:1000'],
            'toolkit' => ['nullable', 'array'],
            'toolkit.*.name' => ['required', 'string', 'max:80'],
            'toolkit.*.role' => ['nullable', 'string', 'max:120'],
            'availability_label' => ['nullable', 'string', 'max:80'],
            'portrait' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:4096'],
            'show_story' => ['boolean'],
            'show_principles' => ['boolean'],
            'show_toolkit' => ['boolean'],
            'show_testimonials' => ['boolean'],
            'show_cta' => ['boolean'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
