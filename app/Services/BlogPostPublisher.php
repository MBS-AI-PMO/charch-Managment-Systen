<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\User;

class BlogPostPublisher
{
    public function save(BlogPost $post, array $data, ?User $actor = null): BlogPost
    {
        $data['body'] = $this->purify($data['body'] ?? '');
        $data['is_published'] = !empty($data['is_published']);

        if ($data['is_published'] && !$post->published_at) {
            $data['published_at'] = $data['published_at'] ?? now();
        }

        if ($actor && !$post->author_id) {
            $data['author_id'] = $actor->id;
        }

        $post->fill($data)->save();

        return $post->fresh();
    }

    protected function purify(string $html): string
    {
        return clean($html, 'cms');
    }
}
