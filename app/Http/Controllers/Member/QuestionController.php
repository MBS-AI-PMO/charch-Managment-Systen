<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('web')->user();
        $this->claimOrphanedQuestions($user);

        $items = ContactMessage::query()
            ->where('user_id', $user->id)
            ->withCount('replies')
            ->latest()
            ->paginate(20);

        return view('member.questions.index', compact('items'));
    }

    public function show(ContactMessage $question)
    {
        $user = Auth::guard('web')->user();
        $this->claimOrphanedQuestions($user);

        abort_unless((int) $question->user_id === (int) $user->id, 404);

        $question->load(['replies.author']);

        return view('member.questions.show', ['question' => $question]);
    }

    private function claimOrphanedQuestions($user): void
    {
        ContactMessage::query()
            ->whereNull('user_id')
            ->where('email', $user->email)
            ->update(['user_id' => $user->id]);
    }
}
