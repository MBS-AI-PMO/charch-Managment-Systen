<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('manage-menus') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('menu')?->id;

        return [
            'name' => 'required|string|max:120',
            'slug' => "required|alpha_dash|max:120|unique:menus,slug,$id",
        ];
    }
}
