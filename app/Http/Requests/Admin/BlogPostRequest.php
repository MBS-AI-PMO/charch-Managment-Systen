<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('manage-blog') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('post')?->id;

        return [
            'title' => 'required|string|max:200',
            'slug' => "nullable|alpha_dash|max:200|unique:blog_posts,slug,$id",
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string',
            'category_id' => 'nullable|exists:blog_categories,id',
            'featured_image_path' => 'nullable|string|max:500',
            'featured_image_file' => 'nullable|file|image|max:8192',
            'meta_title' => 'nullable|string|max:200',
            'meta_description' => 'nullable|string|max:500',
            'is_published' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_published' => $this->boolean('is_published'),
        ]);
    }
}
