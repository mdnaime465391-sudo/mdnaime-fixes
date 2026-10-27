<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    protected function prepareForValidation(): void
    {
        $features = collect($this->input('features', []))
            ->map(fn ($item) => is_string($item) ? trim($item) : $item)
            ->filter(fn ($item) => filled($item))
            ->values()
            ->all();

        $this->merge([
            'slug' => Str::slug($this->input('slug') ?: $this->input('title')),
            'features' => $features,
            'is_published' => $this->boolean('is_published'),
            'sort_order' => (int) $this->input('sort_order', 0),
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:191'],
            'slug' => ['required', 'string', 'max:191', 'alpha_dash', Rule::unique('services', 'slug')->ignore($this->route('service')?->id)],
            'short_description' => ['required', 'string', 'max:255'],
            'long_description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:64'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string', 'max:191'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['boolean'],
        ];
    }
}
