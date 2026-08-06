<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class PrayerRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:200',
            'body' => 'required|string|max:5000',
            'is_public' => 'nullable|boolean',
            'is_anonymous' => 'nullable|boolean',
        ];
    }
}
