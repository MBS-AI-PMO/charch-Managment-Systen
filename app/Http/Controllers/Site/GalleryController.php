<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\MediaFolder;
use App\Models\Page;

class GalleryController extends Controller
{
    public function index()
    {
        $page = Page::where('slug', 'gallery')->published()->first();

        $albums = MediaFolder::query()
            ->whereHas('media', fn ($q) => $q->where('mime_type', 'like', 'image/%'))
            ->withCount(['media as images_count' => fn ($q) => $q->where('mime_type', 'like', 'image/%')])
            ->orderBy('name')
            ->get()
            ->each(function (MediaFolder $album) {
                $album->setRelation(
                    'cover',
                    $album->media()
                        ->where('mime_type', 'like', 'image/%')
                        ->latest('id')
                        ->first()
                );
            });

        $photos = Media::query()
            ->where('mime_type', 'like', 'image/%')
            ->latest('id')
            ->paginate(24);

        return view('site.gallery.index', compact('page', 'albums', 'photos'));
    }

    public function show(MediaFolder $folder)
    {
        $photos = $folder->media()
            ->where('mime_type', 'like', 'image/%')
            ->latest('id')
            ->paginate(36);

        abort_if($photos->total() === 0, 404);

        $page = Page::where('slug', 'gallery')->published()->first();

        return view('site.gallery.show', compact('page', 'folder', 'photos'));
    }
}
