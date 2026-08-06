<?php

namespace App\Http\Requests\Site;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'email' => 'required|email:rfc|max:160',
            'phone' => 'nullable|string|max:32',
            'subject' => 'required|string|max:160',
            'message' => 'required|string|max:5000',
            'website' => 'nullable|size:0',
            'source' => 'nullable|string|in:ask_question',
            'g-recaptcha-response' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please tell us your name.',
            'name.max' => 'Name is a little too long (120 characters max).',
            'email.required' => 'We need an email address to reply to.',
            'email.email' => 'That email address does not look right.',
            'email.max' => 'Email is too long (160 characters max).',
            'phone.max' => 'Phone number is too long (32 characters max).',
            'subject.required' => 'Please choose a subject.',
            'subject.max' => 'Subject is too long (160 characters max).',
            'message.required' => 'Please write a short message so we know how to help.',
            'message.max' => 'Message is too long (5000 characters max).',
            'website.size' => 'Spam check failed.',
        ];
    }
}
