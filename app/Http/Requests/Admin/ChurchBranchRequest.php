<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ChurchBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('manage-pages') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('church')?->id ?? $this->route('church_branch')?->id;

        return [
            'name' => 'required|string|max:200',
            'slug' => "nullable|alpha_dash|max:200|unique:church_branches,slug,$id",
            'city' => 'nullable|string|max:200',
            'role' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:200',
            'services' => 'nullable|string',
            'note' => 'nullable|string|max:1000',
            'pastor' => 'nullable|string|max:200',
            'language' => 'nullable|string|max:100',
            'hero_sub' => 'nullable|string|max:500',
            'about' => 'nullable|string',
            'expect' => 'nullable|string',
            'ministries' => 'nullable|string',
            'families' => 'nullable|string',
            'visit' => 'nullable|string',
            'getting_here' => 'nullable|string',
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
