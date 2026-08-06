<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SermonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('manage-sermons') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('sermon')?->id;

        return [
            'title' => 'required|string|max:200',
            'slug' => "nullable|alpha_dash|max:200|unique:sermons,slug,$id",
            'summary' => 'nullable|string|max:1000',
            'body' => 'nullable|string',
            'series_id' => 'nullable|exists:sermon_series,id',
            'speaker_id' => 'nullable|exists:sermon_speakers,id',
            'scripture_reference' => 'nullable|string|max:200',
            'preached_on' => 'nullable|date',
            'audio_url' => 'nullable|url|max:500',
            'video_url' => 'nullable|url|max:500',
            'thumbnail_path' => 'nullable|string|max:500',
            'thumbnail_file' => 'nullable|file|image|max:8192',
            'is_published' => 'nullable|boolean',
            'downloads_enabled' => 'nullable|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_published' => $this->boolean('is_published'),
            'downloads_enabled' => $this->boolean('downloads_enabled'),
        ]);
    }
}
