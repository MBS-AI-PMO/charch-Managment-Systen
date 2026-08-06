<?php

namespace App\Http\Controllers\Admin;

use App\Models\Media;
use App\Models\MediaFolder;
use App\Services\MediaUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * Media library (M8). Lists folders and paginated assets, accepts
 * single or batched uploads, and returns JSON for the CKEditor adapter
 * + the popup-style picker.
 */
class MediaController extends Controller
{
    public function index(Request $req)
    {
        // Load every folder once; the recursive blade partial walks the tree
        // via the in-memory `children` relation populated by parentId grouping.
        $allFolders = MediaFolder::orderBy('name')->get();
        $byParent = $allFolders->groupBy('parent_id');
        foreach ($allFolders as $f) {
            $f->setRelation('children', $byParent->get($f->id) ?? collect());
        }
        $folders = $allFolders;

        $currentFolder = null;
        $query = Media::query()->latest();

        if ($folderId = $req->query('folder')) {
            $currentFolder = MediaFolder::find($folderId);
            if ($currentFolder) {
                $query->where('folder_id', $currentFolder->id);
            }
        } else {
            // "All files" view — show everything, but allow ?folder=root to
            // filter only un-foldered uploads.
            if ($req->query('folder') === 'root') {
                $query->whereNull('folder_id');
            }
        }

        $items = $query->paginate(48)->withQueryString();

        return view('admin.media.index', [
            'folders' => $folders,
            'currentFolder' => $currentFolder,
            'items' => $items,
            'picker' => (bool) $req->query('picker'),
        ]);
    }

    public function store(Request $req, MediaUploader $uploader)
    {
        // Accept either `files[]` (multiple) or `file` (single). Normalise
        // to an array up-front so the loop is uniform.
        $files = $req->file('files');
        if (! $files) {
            $single = $req->file('file');
            $files = $single ? [$single] : [];
        }
        if (empty($files)) {
            if ($req->wantsJson() || $req->expectsJson()) {
                return response()->json(['message' => 'No file uploaded.'], 422);
            }
            throw ValidationException::withMessages(['file' => 'No file uploaded.']);
        }

        $folder = null;
        if ($folderId = $req->input('folder_id')) {
            $folder = MediaFolder::find($folderId);
        }

        $created = [];
        foreach ($files as $file) {
            if (! $file->isValid()) {
                continue;
            }
            $media = $uploader->store($file, $folder, auth('admin')->user());
            $created[] = [
                'id' => $media->id,
                'url' => Storage::disk($media->disk)->url($media->path),
                'alt' => $media->alt_text,
                'filename' => $media->filename,
                'mime_type' => $media->mime_type,
                'size' => $media->size,
                'width' => $media->width,
                'height' => $media->height,
            ];
        }

        if ($req->wantsJson() || $req->expectsJson()) {
            return response()->json(['items' => $created]);
        }

        return back()->with('success', count($created).' file(s) uploaded.');
    }

    public function update(Request $req, Media $media)
    {
        $data = $req->validate([
            'alt_text' => 'nullable|string|max:255',
        ]);
        $media->update($data);

        if ($req->wantsJson() || $req->expectsJson()) {
            return response()->json(['ok' => true, 'alt' => $media->alt_text]);
        }
        return back()->with('success', 'Alt text saved.');
    }

    public function destroy(Media $media)
    {
        if ($media->path && Storage::disk($media->disk ?: 'public')->exists($media->path)) {
            Storage::disk($media->disk ?: 'public')->delete($media->path);
        }
        $media->delete();
        return back()->with('success', 'File removed.');
    }
}
