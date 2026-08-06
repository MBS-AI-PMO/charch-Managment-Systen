<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\FeedPost;
use App\Models\FeedReaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeedController extends Controller
{
    public function index(Request $request)
    {
        $posts = FeedPost::with('author')
            ->published()
            ->orderBy('pinned', 'desc')
            ->latest('published_at')
            ->paginate(10);

        $myReactions = FeedReaction::where('user_id', $request->user()->id)
            ->whereIn('feed_post_id', $posts->pluck('id'))
            ->pluck('kind', 'feed_post_id')
            ->toArray();

        return view('member.feed.index', compact('posts', 'myReactions'));
    }

    public function react(Request $request, FeedPost $post)
    {
        $data = $request->validate([
            'kind' => ['required', Rule::in(['heart', 'pray', 'amen'])],
        ]);

        $userId = $request->user()->id;
        $existing = FeedReaction::where('feed_post_id', $post->id)
            ->where('user_id', $userId)
            ->first();

        if ($existing && $existing->kind === $data['kind']) {
            $existing->delete();
        } else {
            FeedReaction::updateOrCreate(
                ['feed_post_id' => $post->id, 'user_id' => $userId],
                ['kind' => $data['kind'], 'created_at' => now()],
            );
        }

        return back();
    }
}
