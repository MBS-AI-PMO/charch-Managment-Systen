<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // M8: profile-edit form also hosts an email-preferences sub-form. When
        // that sub-form posts with _prefs_only=1, we skip the profile-field
        // validation entirely; the controller handles persistence.
        if ($this->boolean('_prefs_only')) {
            return [
                '_prefs_only' => 'required',
                'email_reminder_event_24h' => 'nullable|in:0,1',
                'email_weekly_digest' => 'nullable|in:0,1',
            ];
        }

        $id = $this->user()->id;

        return [
            'name' => 'required|string|max:120',
            'email' => "required|email:rfc|max:160|unique:users,email,$id",
            'phone' => 'nullable|string|max:32',
            'bio' => 'nullable|string|max:280',
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png,webp,gif|max:5120',
            'remove_avatar' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.image' => 'Please choose a photo (JPG, PNG or WebP).',
            'avatar.mimes' => 'Please choose a photo (JPG, PNG or WebP).',
            'avatar.max' => 'That photo is too large. Please use a file under 5 MB.',
        ];
    }
}
