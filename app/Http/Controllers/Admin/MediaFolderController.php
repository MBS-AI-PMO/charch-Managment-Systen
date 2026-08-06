<?php

namespace App\Http\Controllers\Admin;

use App\Models\MediaFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Folder CRUD for the media library (M8). Folder paths are kept in
 * sync with the parent chain so URLs remain stable across renames.
 */
class MediaFolderController extends Controller
{
    public function store(Request $req)
    {
        $data = $req->validate([
            'name' => ['required', 'string', 'max:80'],
            'parent_id' => ['nullable', 'exists:media_folders,id'],
        ]);

        $parent = $data['parent_id'] ? MediaFolder::find($data['parent_id']) : null;
        $slug = $this->uniqueSlug(Str::slug($data['name']), $parent?->id);
        $path = $parent ? trim($parent->path, '/').'/'.$slug : $slug;

        $folder = MediaFolder::create([
            'name' => $data['name'],
            'slug' => $slug,
            'path' => $path,
            'parent_id' => $parent?->id,
        ]);

        if ($req->wantsJson() || $req->expectsJson()) {
            return response()->json(['folder' => $folder]);
        }
        return back()->with('success', 'Folder created.');
    }

    public function update(Request $req, MediaFolder $folder)
    {
        $data = $req->validate([
            'name' => ['required', 'string', 'max:80'],
        ]);

        $newSlug = $this->uniqueSlug(Str::slug($data['name']), $folder->parent_id, $folder->id);
        $parent = $folder->parent_id ? MediaFolder::find($folder->parent_id) : null;
        $newPath = $parent ? trim($parent->path, '/').'/'.$newSlug : $newSlug;

        $folder->update([
            'name' => $data['name'],
            'slug' => $newSlug,
            'path' => $newPath,
        ]);

        // Cascade path updates to descendants.
        $this->rebuildChildPaths($folder);

        return back()->with('success', 'Folder renamed.');
    }

    public function destroy(MediaFolder $folder)
    {
        if ($folder->media()->exists() || $folder->children()->exists()) {
            abort(422, 'Folder is not empty.');
        }
        $folder->delete();
        return back()->with('success', 'Folder removed.');
    }

    /**
     * Generate a slug unique within the given parent (NULL for root).
     */
    protected function uniqueSlug(string $base, ?int $parentId, ?int $ignoreId = null): string
    {
        $slug = $base ?: 'folder';
        $i = 1;
        $query = function (string $candidate) use ($parentId, $ignoreId) {
            $q = MediaFolder::where('slug', $candidate);
            $q = $parentId === null ? $q->whereNull('parent_id') : $q->where('parent_id', $parentId);
            if ($ignoreId) {
                $q->where('id', '!=', $ignoreId);
            }
            return $q->exists();
        };
        while ($query($slug)) {
            $i++;
            $slug = $base.'-'.$i;
        }
        return $slug;
    }

    /**
     * Recursively rewrite descendant paths after a rename.
     */
    protected function rebuildChildPaths(MediaFolder $folder): void
    {
        foreach ($folder->children()->get() as $child) {
            $child->path = trim($folder->path, '/').'/'.$child->slug;
            $child->save();
            $this->rebuildChildPaths($child);
        }
    }
}
