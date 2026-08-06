<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SermonSeriesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('manage-sermons') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('series')?->id ?? $this->route('serie')?->id;

        return [
            'name' => 'required|string|max:200',
            'slug' => "nullable|alpha_dash|max:200|unique:sermon_series,slug,$id",
            'description' => 'nullable|string|max:2000',
            'cover_image_path' => 'nullable|string|max:500',
            'cover_image_file' => 'nullable|file|image|max:8192',
        ];
    }
}
