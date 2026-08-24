<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HeroSlideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('manage-pages') ?? false;
    }

    public function rules(): array
    {
        return [
            'position' => 'nullable|integer|min:0',
            'image_path' => 'nullable|string|max:500',
            'image_file' => 'nullable|file|image|max:8192',
            'eyebrow' => 'nullable|string|max:200',
            'heading' => 'required|string|max:300',
            'sub' => 'nullable|string',
            'primary_cta_url' => 'nullable|string|max:500',
            'primary_cta_label' => 'nullable|string|max:100',
            'secondary_cta_url' => 'nullable|string|max:500',
            'secondary_cta_label' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
