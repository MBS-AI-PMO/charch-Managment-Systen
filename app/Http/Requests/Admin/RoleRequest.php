<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('manage-roles') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('role')?->id;

        return [
            'name' => "required|string|max:120|unique:roles,name,$id",
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ];
    }
}
