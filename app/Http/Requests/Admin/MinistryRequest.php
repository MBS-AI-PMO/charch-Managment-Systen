<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MinistryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('manage-ministries') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('ministry')?->id;

        return [
            'name' => 'required|string|max:200',
            'slug' => "nullable|alpha_dash|max:200|unique:ministries,slug,$id",
            'summary' => 'nullable|string|max:500',
            'body' => 'nullable|string',
            'leader_name' => 'nullable|string|max:200',
            'contact_email' => 'nullable|email|max:200',
            'cover_image_path' => 'nullable|string|max:500',
            'cover_image_file' => 'nullable|file|image|max:8192',
            'sort_order' => 'nullable|integer|min:0',
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
