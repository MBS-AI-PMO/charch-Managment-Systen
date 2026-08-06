<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PrayerRequestUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('manage-prayer-requests') ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:pending,praying,answered,closed',
            'assigned_to' => 'nullable|exists:users,id',
            'admin_notes' => 'nullable|string|max:10000',
        ];
    }
}
