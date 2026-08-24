<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

abstract class BrandedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function envelope(): \Illuminate\Mail\Mailables\Envelope
    {
        $brandName = settings('brand.name', config('app.name', 'Our Church'));
        $fromEmail = config('mail.from.address');
        $replyTo = config('mail.reply_to.address')
            ?: (settings('contact.email') ?: $fromEmail);

        return new \Illuminate\Mail\Mailables\Envelope(
            from: new \Illuminate\Mail\Mailables\Address($fromEmail, $brandName),
            replyTo: [new \Illuminate\Mail\Mailables\Address($replyTo, $brandName)],
            subject: $this->subjectLine(),
        );
    }

    public function content(): \Illuminate\Mail\Mailables\Content
    {
        return new \Illuminate\Mail\Mailables\Content(
            view: $this->viewName(),
            with: $this->viewData() + [
                'brandName' => settings('brand.name', config('app.name')),
                'brandColor' => settings('brand.color.primary', '#7C3AED'),
                'address' => settings('contact.address', ''),
            ],
        );
    }

    abstract protected function subjectLine(): string;
    abstract protected function viewName(): string;
    abstract protected function viewData(): array;
}
