<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use App\Support\CertificateTemplates;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('manage-certificates') ?? false;
    }

    public function rules(): array
    {
        return [
            'template' => ['required', 'string', Rule::in(CertificateTemplates::keys())],
            'recipient_name' => ['required', 'string', 'max:180'],
            'assigned_by' => ['required', 'string', 'max:180'],
            'issued_on' => ['required', 'date'],
            'title' => ['nullable', 'string', 'max:180'],
            'user_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('is_admin', false)),
            ],
        ];
    }

    public function member(): ?User
    {
        $id = $this->validated('user_id');

        return $id ? User::query()->where('is_admin', false)->find($id) : null;
    }

    /** Fields persisted on the certificates table (excludes assign target). */
    public function certificateData(): array
    {
        return collect($this->validated())->except('user_id')->all();
    }
}
