<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FeedPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('manage-community-feed') ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:200',
            'body' => 'required|string|max:20000',
            'image' => 'nullable|file|image|max:10240',
            'image_path' => 'nullable|string|max:255',
            'pinned' => 'nullable|boolean',
            'published_at' => 'nullable|date',
            'broadcast' => 'nullable|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'pinned' => $this->boolean('pinned'),
            'broadcast' => $this->boolean('broadcast'),
        ]);
    }
}
