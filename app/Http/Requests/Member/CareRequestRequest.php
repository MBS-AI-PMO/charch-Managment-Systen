<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class CareRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => 'required|in:illness,grief,financial,food,other',
            'message' => 'required|string|max:5000',
            'share_with_team' => 'nullable|boolean',
        ];
    }
}
