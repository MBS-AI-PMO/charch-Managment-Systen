<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('manage-settings') ?? false;
    }

    public function rules(): array
    {
        return [
            'brand.name' => 'nullable|string|max:200',
            'brand.tagline' => 'nullable|string|max:200',
            'brand.logo' => 'nullable|file|max:4096|mimes:jpg,jpeg,png,gif,webp,svg',
            'brand.favicon' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,gif,webp,ico,svg',
            'brand.logo_path' => 'nullable|string|max:500',
            'brand.favicon_path' => 'nullable|string|max:500',
            'brand.color.primary' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'brand.color.secondary' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'contact.address' => 'nullable|string|max:500',
            'contact.phone' => 'nullable|string|max:50',
            'contact.email' => 'nullable|email|max:200',
            'contact.service_times' => 'nullable|string|max:1000',
            'social.facebook' => 'nullable|string|max:500',
            'social.instagram' => 'nullable|string|max:500',
            'social.youtube' => 'nullable|string|max:500',
            'social.x' => 'nullable|string|max:500',
            'footer.about' => 'nullable|string|max:1000',
            'footer.copyright' => 'nullable|string|max:200',
            'seo.default_title_suffix' => 'nullable|string|max:200',
            'seo.default_description' => 'nullable|string|max:500',
            'seo.analytics_script' => 'nullable|string|max:5000',
            'mail.from_address' => 'nullable|email|max:200',
            'mail.host' => 'nullable|string|max:200',
            'mail.port' => 'nullable|integer',
            'mail.encryption' => 'nullable|string|max:10',
            'reminders.event_24h_enabled' => 'nullable|in:0,1',
            'reminders.weekly_digest_enabled' => 'nullable|in:0,1',
            'reminders.weekly_digest_day' => 'nullable|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'reminders.weekly_digest_hour' => 'nullable|integer|min:0|max:23',
            'reminders.admin_daily_digest_enabled' => 'nullable|in:0,1',
        ];
    }
}
