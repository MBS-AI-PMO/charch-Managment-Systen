<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SermonSpeakerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('manage-sermons') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('speaker')?->id;

        return [
            'name' => 'required|string|max:200',
            'slug' => "nullable|alpha_dash|max:200|unique:sermon_speakers,slug,$id",
            'role' => 'nullable|string|max:200',
            'bio' => 'nullable|string|max:2000',
            'photo_path' => 'nullable|string|max:500',
            'photo_file' => 'nullable|file|image|max:8192',
        ];
    }
}
