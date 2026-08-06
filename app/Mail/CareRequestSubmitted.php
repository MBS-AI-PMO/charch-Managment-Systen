<?php

namespace App\Mail;

use App\Models\CareRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CareRequestSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CareRequest $careRequest) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Knock for Help: '.ucfirst($this->careRequest->category),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.care-submitted',
            with: ['c' => $this->careRequest],
        );
    }
}
