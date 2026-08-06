<?php

namespace App\Mail;

use App\Models\User;

class WeeklyDigestMail extends BrandedMail
{
    public function __construct(public User $user, public array $payload) {}

    protected function subjectLine(): string
    {
        return 'This week at '.settings('brand.name', 'our church');
    }

    protected function viewName(): string
    {
        return 'mail.weekly_digest';
    }

    protected function viewData(): array
    {
        return [
            'user' => $this->user,
            'payload' => $this->payload,
        ];
    }
}
