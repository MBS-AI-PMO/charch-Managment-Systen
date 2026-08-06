<?php

namespace App\Mail;

use App\Models\ContactMessage;
use App\Models\ContactMessageReply;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuestionReplied extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactMessage $message,
        public ContactMessageReply $reply,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reply to your question: '.$this->message->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.question-replied',
            with: [
                'm' => $this->message,
                'reply' => $this->reply,
            ],
        );
    }
}
