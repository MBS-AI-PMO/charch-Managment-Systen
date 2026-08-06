<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (string) settings('member.registration_enabled', '1') === '1';
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'email' => 'required|email:rfc|max:160|unique:users,email',
            'password' => ['required', Password::defaults(), 'confirmed'],
            'website' => 'nullable|size:0',
        ];
    }
}
