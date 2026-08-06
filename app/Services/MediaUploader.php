<?php

namespace App\Services;

use App\Models\Media;
use App\Models\MediaFolder;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

/**
 * Persists an UploadedFile to the `public` disk, downscales/re-encodes
 * raster images, and inserts a Media row. Used by the media-library
 * controller and by CKEditor's upload adapter.
 */
class MediaUploader
{
    /**
     * Allowed MIME types (whitelist). PDFs are stored raw; raster images
     * are normalised to JPEG to strip metadata and limit attack surface.
     */
    protected const ALLOWED_MIMES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
        'application/pdf',
    ];

    /** Extensions that must never be written, even with a fake MIME. */
    protected const DENIED_EXTS = ['php', 'phtml', 'phar', 'exe', 'sh', 'bat', 'svg'];

    /** Hard upload size cap (10 MB). */
    protected const MAX_BYTES = 10 * 1024 * 1024;

    /** Above this width images are downscaled before storage. */
    protected const MAX_WIDTH = 2400;

    /**
     * Validate, normalise, and persist the uploaded file. Returns the
     * created Media model.
     */
    public function store(UploadedFile $file, ?MediaFolder $folder = null, ?User $actor = null): Media
    {
        $this->guard($file);

        $ext = strtolower($file->getClientOriginalExtension() ?: 'bin');
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $slug = Str::slug($baseName) ?: 'file';
        $name = Str::random(8).'_'.$slug.'.'.$ext;

        $dir = trim(($folder?->path ?? 'uploads'), '/');
        $dir = $dir === '' ? 'uploads' : $dir;
        $path = $dir.'/'.$name;

        $bytes = $file->getContent();
        $mime = $file->getMimeType();

        // Raster images get downscaled + re-encoded as JPEG (strips metadata).
        if (in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            $img = Image::read($bytes);
            if ($img->width() > self::MAX_WIDTH) {
                $img->scaleDown(width: self::MAX_WIDTH);
            }
            $bytes = (string) $img->toJpeg(quality: 85);
            $mime = 'image/jpeg';
            if (! in_array($ext, ['jpg', 'jpeg'], true)) {
                $path = preg_replace('/\.\w+$/', '.jpg', $path);
                $name = preg_replace('/\.\w+$/', '.jpg', $name);
                $ext = 'jpg';
            }
        }

        Storage::disk('public')->put($path, $bytes);

        $size = [null, null];
        if (str_starts_with($mime, 'image/')) {
            $probe = @getimagesizefromstring($bytes);
            if (is_array($probe)) {
                $size = [$probe[0] ?? null, $probe[1] ?? null];
            }
        }
        [$w, $h] = $size;

        return Media::create([
            'folder_id' => $folder?->id,
            'disk' => 'public',
            'path' => $path,
            'filename' => $name,
            'mime_type' => $mime,
            'size' => strlen($bytes),
            'width' => $w,
            'height' => $h,
            'alt_text' => null,
            'uploaded_by' => $actor?->id,
        ]);
    }

    /**
     * Reject uploads that would be unsafe to store. Throws an HTTP 422
     * via abort() so controllers can let the response bubble up.
     */
    protected function guard(UploadedFile $file): void
    {
        if (! in_array($file->getMimeType(), self::ALLOWED_MIMES, true)) {
            abort(422, 'Unsupported file type: '.$file->getMimeType());
        }
        if ($file->getSize() > self::MAX_BYTES) {
            abort(422, 'File exceeds the 10 MB upload limit.');
        }
        $ext = strtolower($file->getClientOriginalExtension());
        if (in_array($ext, self::DENIED_EXTS, true)) {
            abort(422, 'Forbidden file extension: .'.$ext);
        }
    }
}
