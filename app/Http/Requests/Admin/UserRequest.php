<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('manage-users') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('user')?->id;
        $creating = $this->isMethod('post') && !$id;

        return [
            'name' => 'required|string|max:120',
            'email' => "required|email|max:200|unique:users,email,$id",
            'password' => [$creating ? 'required' : 'nullable', 'confirmed', Password::defaults()],
            'roles' => 'nullable|array',
            'roles.*' => 'string',
            'is_admin' => 'nullable|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_admin' => $this->boolean('is_admin', true),
        ]);
    }
}
