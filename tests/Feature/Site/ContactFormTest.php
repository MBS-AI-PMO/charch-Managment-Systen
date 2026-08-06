<?php

use App\Mail\ContactSubmitted;
use App\Models\ContactMessage;
use App\Models\Page;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Page::factory()->create(['slug' => 'contact-us']);
});

it('stores a contact message and queues the notification email', function () {
    Mail::fake();

    $this->post('/contact', [
        'name' => 'Jane Visitor',
        'email' => 'jane@example.com',
        'subject' => 'Hello',
        'message' => 'Just saying hi, hoping to learn more.',
    ])->assertRedirect();

    expect(ContactMessage::where('email', 'jane@example.com')->exists())->toBeTrue();
    Mail::assertSent(ContactSubmitted::class, fn ($mail) => $mail->message->email === 'jane@example.com');
});

it('silently drops a honeypot submission', function () {
    Mail::fake();

    $this->post('/contact', [
        'name' => 'Bot',
        'email' => 'bot@example.com',
        'subject' => 'Spam',
        'message' => 'buy buy buy',
        'website' => 'http://spammer.example',
    ])->assertRedirect();

    expect(ContactMessage::count())->toBe(0);
    Mail::assertNothingSent();
});

it('rejects an invalid email', function () {
    $this->from('/contact')
        ->post('/contact', [
            'name' => 'Bad Email',
            'email' => 'not-an-email',
            'subject' => 'Hi',
            'message' => 'Hello.',
        ])->assertSessionHasErrors('email');

    expect(ContactMessage::count())->toBe(0);
});

it('throttles after 5 submissions in an hour', function () {
    foreach (range(1, 5) as $i) {
        $this->post('/contact', [
            'name' => 'Person',
            'email' => 'p'.$i.'@example.com',
            'subject' => 'Hi',
            'message' => 'Hello.',
        ])->assertRedirect();
    }

    $this->post('/contact', [
        'name' => 'Person',
        'email' => 'p6@example.com',
        'subject' => 'Hi',
        'message' => 'Hello.',
    ])->assertStatus(429);
});
