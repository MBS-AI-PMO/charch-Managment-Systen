<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\MessageReplyRequest;
use App\Mail\QuestionReplied;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MessageController extends Controller
{
    public function index(Request $req)
    {
        $q = ContactMessage::query()->withCount('replies');

        if ($status = $req->query('status')) {
            if ($status === 'unread') {
                $q->whereNull('read_at');
            }
            if ($status === 'read') {
                $q->whereNotNull('read_at');
            }
            if ($status === 'awaiting') {
                $q->whereNull('replied_at');
            }
            if ($status === 'replied') {
                $q->whereNotNull('replied_at');
            }
        }

        if ($s = $req->query('search')) {
            $q->where(function ($qq) use ($s) {
                $qq->where('name', 'like', "%$s%")
                    ->orWhere('email', 'like', "%$s%")
                    ->orWhere('subject', 'like', "%$s%")
                    ->orWhere('message', 'like', "%$s%");
            });
        }

        $messages = $q->latest()->paginate(25)->withQueryString();
        $unreadCount = ContactMessage::whereNull('read_at')->count();
        $awaitingCount = ContactMessage::whereNull('replied_at')->count();

        return view('admin.messages.index', compact('messages', 'unreadCount', 'awaitingCount'));
    }

    public function show(ContactMessage $message)
    {
        if (is_null($message->read_at)) {
            $message->update(['read_at' => now()]);
        }

        $message->load(['replies.author', 'user']);

        return view('admin.messages.show', compact('message'));
    }

    public function reply(MessageReplyRequest $request, ContactMessage $message)
    {
        $reply = $message->replies()->create([
            'user_id' => $request->user('admin')?->id,
            'is_staff' => true,
            'body' => $request->validated('body'),
        ]);

        $message->update([
            'replied_at' => now(),
            'read_at' => $message->read_at ?? now(),
            'is_public' => $request->boolean('is_public'),
        ]);

        try {
            Mail::to($message->email)->send(new QuestionReplied($message, $reply));
        } catch (\Throwable $e) {
            logger()->error('Question reply mail failed', ['err' => $e->getMessage()]);
        }

        $msg = $request->boolean('is_public')
            ? 'Reply sent and published on the Q&A page.'
            : 'Reply sent. The visitor has been notified by email.';

        return back()->with('success', $msg);
    }

    public function togglePublic(ContactMessage $message)
    {
        if (! $message->isReplied()) {
            return back()->with('error', 'Reply first before publishing to Q&A.');
        }

        $message->update(['is_public' => ! $message->is_public]);

        return back()->with(
            'success',
            $message->is_public
                ? 'Published on the public Q&A page.'
                : 'Removed from the public Q&A page.'
        );
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();

        return redirect()->route('admin.messages.index')->with('success', 'Message deleted.');
    }
}
