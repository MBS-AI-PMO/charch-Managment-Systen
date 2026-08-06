<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BlogCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('manage-blog') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('category')?->id;

        return [
            'name' => 'required|string|max:120',
            'slug' => "nullable|alpha_dash|max:120|unique:blog_categories,slug,$id",
        ];
    }
}
