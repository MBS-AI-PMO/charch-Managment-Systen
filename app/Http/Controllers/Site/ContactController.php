<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\ContactRequest;
use App\Mail\ContactSubmitted;
use App\Models\ContactMessage;
use App\Models\Page;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        $page = Page::where('slug', 'contact-us')->published()->first();

        return view('site.contact', compact('page'));
    }

    public function submit(ContactRequest $request)
    {
        $fromAsk = $request->input('source') === 'ask_question';

        // Honeypot: silently drop bot submissions.
        if ($request->filled('website')) {
            return $this->contactRedirect($fromAsk);
        }

        $data = $request->safe()->except(['website', 'g-recaptcha-response', 'source']);

        $user = $request->user('web');
        $userId = $user?->id;

        if (! $userId) {
            $userId = User::query()
                ->where('email', $data['email'])
                ->where('is_admin', false)
                ->value('id');
        }

        $message = ContactMessage::create($data + [
            'user_id' => $userId,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        try {
            Mail::to(settings('contact.email', config('mail.from.address')))
                ->send(new ContactSubmitted($message));
        } catch (\Throwable $e) {
            logger()->error('Contact form mail failed', ['err' => $e->getMessage()]);
        }

        return $this->contactRedirect($fromAsk, (bool) $userId);
    }

    private function contactRedirect(bool $fromAsk, bool $hasAccount = false)
    {
        $success = $fromAsk
            ? ($hasAccount
                ? 'Question submitted. Track replies anytime in My questions.'
                : 'Question submitted. Sign in later with this email to see replies.')
            : 'Thanks — we will be in touch.';

        $redirect = back()->with('success', $success);

        if ($fromAsk) {
            $redirect->with('ask_question_success', true);
        }

        return $redirect;
    }
}
