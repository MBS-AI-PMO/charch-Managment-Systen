<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('manage-pages') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('page')?->id;

        return [
            'title' => 'required|string|max:200',
            'slug' => "required|alpha_dash|max:200|unique:pages,slug,$id",
            'hero_heading' => 'nullable|string|max:200',
            'hero_subheading' => 'nullable|string|max:500',
            'hero_image_path' => 'nullable|string|max:500',
            'hero_image_file' => 'nullable|file|image|max:8192',
            'body' => 'nullable|string',
            'meta_title' => 'nullable|string|max:200',
            'meta_description' => 'nullable|string|max:500',
            'is_published' => 'nullable|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_published' => $this->boolean('is_published'),
        ]);
    }
}
