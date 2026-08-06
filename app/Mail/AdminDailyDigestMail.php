<?php

namespace App\Mail;

use App\Models\User;

class AdminDailyDigestMail extends BrandedMail
{
    public function __construct(public User $admin, public array $payload) {}

    protected function subjectLine(): string
    {
        return 'Daily digest — '.settings('brand.name', 'admin');
    }

    protected function viewName(): string
    {
        return 'mail.admin_daily_digest';
    }

    protected function viewData(): array
    {
        return [
            'admin' => $this->admin,
            'payload' => $this->payload,
        ];
    }
}
