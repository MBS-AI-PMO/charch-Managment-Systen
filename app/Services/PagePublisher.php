<?php

namespace App\Services;

use App\Models\Page;
use App\Models\PageRevision;
use App\Models\User;

class PagePublisher
{
    public function save(Page $page, array $data, ?User $actor = null): Page
    {
        $data['body'] = $this->purify($data['body'] ?? '');

        // Coerce checkbox-style values
        $data['is_published'] = !empty($data['is_published']);

        if ($data['is_published'] && !$page->published_at) {
            $data['published_at'] = now();
        }

        $data['updated_by'] = $actor?->id ?? $page->updated_by;

        $page->fill($data)->save();

        PageRevision::create([
            'page_id' => $page->id,
            'snapshot' => json_encode($page->fresh()->toArray()),
            'user_id' => $actor?->id,
        ]);

        return $page->fresh();
    }

    protected function purify(string $html): string
    {
        return clean($html, 'cms');
    }
}
