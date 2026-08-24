<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FeedPostRequest;
use App\Jobs\SendFeedBroadcast;
use App\Models\FeedPost;
use App\Services\MediaUploader;

class FeedController extends Controller
{
    public function index()
    {
        $posts = FeedPost::with('author')
            ->withCount('reactions')
            ->latest('published_at')
            ->paginate(10);

        return view('admin.feed.index', compact('posts'));
    }

    public function create()
    {
        $post = new FeedPost();

        return view('admin.feed.create', compact('post'));
    }

    public function store(FeedPostRequest $request, MediaUploader $uploader)
    {
        $data = $request->validated();
        $data['body'] = clean($data['body'], 'cms');
        $data['author_id'] = $request->user('admin')->id;
        $data['pinned'] = (bool) ($data['pinned'] ?? false);
        $data['published_at'] = $data['published_at'] ?? now();

        if ($request->hasFile('image')) {
            $media = $uploader->store($request->file('image'), null, $request->user('admin'));
            $data['image_path'] = $media->path;
        } elseif (! empty($data['image_path']) && str_starts_with($data['image_path'], '/storage/')) {
            $data['image_path'] = ltrim(substr($data['image_path'], strlen('/storage/')), '/');
        }

        $broadcast = (bool) ($data['broadcast'] ?? false);
        unset($data['image'], $data['broadcast']);

        $post = FeedPost::create($data);

        if ($broadcast) {
            SendFeedBroadcast::dispatch($post->id);
        }

        return redirect()
            ->route('admin.feed.index')
            ->with('success', 'Feed post published.'.($broadcast ? ' Broadcast queued.' : ''));
    }

    public function show(FeedPost $feed)
    {
        return redirect()->route('admin.feed.edit', $feed);
    }

    public function edit(FeedPost $feed)
    {
        return view('admin.feed.edit', ['post' => $feed]);
    }

    public function update(FeedPostRequest $request, FeedPost $feed, MediaUploader $uploader)
    {
        $data = $request->validated();
        $data['body'] = clean($data['body'], 'cms');
        $data['pinned'] = (bool) ($data['pinned'] ?? false);

        if (empty($data['published_at'])) {
            $data['published_at'] = $feed->published_at ?? now();
        }

        if ($request->hasFile('image')) {
            $media = $uploader->store($request->file('image'), null, $request->user('admin'));
            $data['image_path'] = $media->path;
        } elseif (! empty($data['image_path']) && str_starts_with($data['image_path'], '/storage/')) {
            $data['image_path'] = ltrim(substr($data['image_path'], strlen('/storage/')), '/');
        }

        unset($data['image'], $data['broadcast']);

        $feed->update($data);

        return redirect()
            ->route('admin.feed.index')
            ->with('success', 'Feed post updated.');
    }

    public function destroy(FeedPost $feed)
    {
        $feed->delete();

        return redirect()->route('admin.feed.index')->with('success', 'Feed post deleted.');
    }
}
