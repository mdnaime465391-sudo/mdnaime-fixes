<?php

namespace App\Http\Requests\Site;

use Illuminate\Foundation\Http\FormRequest;

class InquiryRequest extends FormRequest
{
    public const BUSINESS_TYPES = [
        'Med spa / aesthetic clinic',
        'Dermatology clinic',
        'Salon & spa',
        'Wellness brand',
        'Startup / service business',
        'Agency',
        'Other',
    ];

    public const BUDGETS = [
        'Under $2,000',
        '$2,000 – $5,000',
        '$5,000 – $10,000',
        '$10,000 – $20,000',
        '$20,000+',
        'Not sure yet',
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'website' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:191'],
            'business_type' => ['nullable', 'string', 'max:120'],
            'service' => ['nullable', 'string', 'max:120'],
            'budget' => ['nullable', 'string', 'max:60'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'message.min' => 'Tell me a little more — at least a sentence about the project.',
        ];
    }
}
