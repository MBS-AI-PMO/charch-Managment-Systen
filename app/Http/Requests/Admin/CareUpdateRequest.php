<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CareUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('manage-knock-help') ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:open,responding,closed',
            'response_notes' => 'nullable|string|max:20000',
        ];
    }
}
