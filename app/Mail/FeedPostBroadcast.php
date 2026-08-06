<?php

namespace App\Mail;

use App\Models\FeedPost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeedPostBroadcast extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public FeedPost $post) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->post->title ?: 'A new update from the church',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.feed-broadcast',
            with: ['p' => $this->post],
        );
    }
}
