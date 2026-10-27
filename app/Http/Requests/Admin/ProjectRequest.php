<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    protected function prepareForValidation(): void
    {
        $list = function (?array $value): array {
            return collect($value ?? [])
                ->map(fn ($item) => is_string($item) ? trim($item) : $item)
                ->filter(fn ($item) => filled($item))
                ->values()
                ->all();
        };

        $categories = $this->input('categories');

        if (is_string($categories)) {
            $categories = array_map('trim', explode(',', $categories));
        }

        $this->merge([
            'slug' => Str::slug($this->input('slug') ?: $this->input('title')),
            'categories' => $list(is_array($categories) ? $categories : []),
            'features' => $list($this->input('features')),
            'tech_stack' => $list($this->input('tech_stack')),
            'is_published' => $this->boolean('is_published'),
            'sort_order' => (int) $this->input('sort_order', 0),
        ]);
    }

    public function rules(): array
    {
        $projectId = $this->route('project')?->id;

        return [
            'title' => ['required', 'string', 'max:191'],
            'slug' => ['required', 'string', 'max:191', 'alpha_dash', Rule::unique('projects', 'slug')->ignore($projectId)],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['string', 'max:80'],
            'summary' => ['nullable', 'string', 'max:255'],
            'accent_color' => ['nullable', 'string', 'max:48', 'regex:/^(#[0-9a-fA-F]{3,8}|oklch\([^)]*\))$/'],
            'background' => ['nullable', 'string'],
            'challenge' => ['nullable', 'string'],
            'design_approach' => ['nullable', 'string'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string', 'max:191'],
            'tech_stack' => ['nullable', 'array'],
            'tech_stack.*' => ['string', 'max:80'],
            'solution' => ['nullable', 'string'],
            'result' => ['nullable', 'string'],
            'live_preview_url' => ['nullable', 'url', 'max:255'],
            'demo_credentials' => ['nullable', 'string', 'max:255'],
            'demo_notes' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['boolean'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:4096'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:4096'],
        ];
    }
}
