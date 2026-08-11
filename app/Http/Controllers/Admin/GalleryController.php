<?php

namespace App\Http\Controllers\Admin;

use App\Models\Media;
use App\Models\MediaFolder;
use App\Services\MediaUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * Admin Gallery — image-focused manager for the public /gallery page.
 * Reuses Media / MediaFolder storage; folders act as albums.
 */
class GalleryController extends Controller
{
    public function index(Request $req)
    {
        $allFolders = MediaFolder::orderBy('name')->get();
        $byParent = $allFolders->groupBy('parent_id');
        foreach ($allFolders as $f) {
            $f->setRelation('children', $byParent->get($f->id) ?? collect());
        }

        $currentFolder = null;
        $query = Media::query()
            ->where('mime_type', 'like', 'image/%')
            ->latest('id');

        if ($req->query('folder') === 'root') {
            $query->whereNull('folder_id');
        } elseif ($folderId = $req->query('folder')) {
            $currentFolder = MediaFolder::find($folderId);
            if ($currentFolder) {
                $query->where('folder_id', $currentFolder->id);
            }
        }

        $items = $query->paginate(36)->withQueryString();

        return view('admin.gallery.index', [
            'folders' => $allFolders,
            'currentFolder' => $currentFolder,
            'items' => $items,
        ]);
    }

    public function store(Request $req, MediaUploader $uploader)
    {
        $files = $req->file('files');
        if (! $files) {
            $single = $req->file('file');
            $files = $single ? [$single] : [];
        }
        if (empty($files)) {
            throw ValidationException::withMessages(['files' => 'Choose at least one image to upload.']);
        }

        $folder = null;
        if ($folderId = $req->input('folder_id')) {
            $folder = MediaFolder::find($folderId);
        }

        $created = 0;
        foreach ($files as $file) {
            if (! $file->isValid()) {
                continue;
            }
            if (! str_starts_with((string) $file->getMimeType(), 'image/')) {
                continue;
            }
            $uploader->store($file, $folder, auth('admin')->user());
            $created++;
        }

        if ($created === 0) {
            throw ValidationException::withMessages(['files' => 'Only image files (JPG, PNG, WebP, GIF) are allowed.']);
        }

        return back()->with('success', $created.' photo(s) added to the gallery.');
    }

    public function replace(Request $req, Media $media, MediaUploader $uploader)
    {
        abort_unless(str_starts_with((string) $media->mime_type, 'image/'), 404);

        $data = $req->validate([
            'file' => ['required', 'file', 'image', 'max:10240'],
        ]);

        $uploader->replace($media, $data['file'], auth('admin')->user());

        return back()->with('success', 'Photo replaced. The public gallery now shows the new image.');
    }

    public function update(Request $req, Media $media)
    {
        abort_unless(str_starts_with((string) $media->mime_type, 'image/'), 404);

        $data = $req->validate([
            'alt_text' => 'nullable|string|max:255',
        ]);
        $media->update($data);

        return back()->with('success', 'Caption saved.');
    }

    public function destroy(Media $media)
    {
        abort_unless(str_starts_with((string) $media->mime_type, 'image/'), 404);

        if ($media->path && Storage::disk($media->disk ?: 'public')->exists($media->path)) {
            Storage::disk($media->disk ?: 'public')->delete($media->path);
        }
        $media->delete();

        return back()->with('success', 'Photo removed from the gallery.');
    }
}
