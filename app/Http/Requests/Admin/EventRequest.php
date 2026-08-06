<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('manage-events') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('event')?->id;

        return [
            'title' => 'required|string|max:200',
            'slug' => "nullable|alpha_dash|max:200|unique:events,slug,$id",
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:200',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'cover_image_path' => 'nullable|string|max:500',
            'cover_image_file' => 'nullable|file|image|max:8192',
            'registration_url' => 'nullable|url|max:500',
            'is_published' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_published' => $this->boolean('is_published'),
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }
}
