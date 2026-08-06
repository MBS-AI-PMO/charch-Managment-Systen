<?php

namespace App\Jobs;

use App\Mail\FeedPostBroadcast;
use App\Models\FeedPost;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendFeedBroadcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $postId) {}

    public function handle(): void
    {
        $post = FeedPost::find($this->postId);

        if (! $post) {
            return;
        }

        User::whereNotNull('email_verified_at')
            ->chunkById(50, function ($users) use ($post) {
                foreach ($users as $user) {
                    Mail::to($user->email)->send(new FeedPostBroadcast($post));
                }
            });
    }
}
