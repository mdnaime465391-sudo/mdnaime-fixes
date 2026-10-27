<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Image pipeline: validate → resize → convert to WebP → store on the
 * `public` disk. Optionally produce a thumbnail.
 *
 * Requires the GD extension (enabled by default on most PHP installs).
 */
class ImageService
{
    public const MAX_BYTES = 4 * 1024 * 1024; // 4 MB

    public const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'image/webp', 'image/avif'];

    /**
     * @return string|null relative path on the `public` disk, e.g. "projects/abc123.webp"
     */
    public function storeAsWebp(UploadedFile $file, string $directory, ?int $maxWidth = 1920, ?int $thumbWidth = 640): ?string
    {
        if (! in_array($file->getMimeType(), self::ALLOWED_MIMES, true) || $file->getSize() > self::MAX_BYTES) {
            return null;
        }

        $source = $this->createImage($file);
        if ($source === null) {
            return null;
        }

        $path = (string) Str::uuid();

        $full = $this->resizeAndEncode($source, $maxWidth);
        Storage::disk('public')->put("{$directory}/{$path}.webp", $full);

        if ($thumbWidth !== null) {
            $thumb = $this->resizeAndEncode($source, $thumbWidth);
            Storage::disk('public')->put("{$directory}/{$path}-thumb.webp", $thumb);
        }

        imagedestroy($source);

        return "{$directory}/{$path}.webp";
    }

    /**
     * Store a small brand asset (favicon, logo mark) without re-encoding.
     * SVG is deliberately rejected — uploaded SVG can carry script.
     */
    public function storeOriginal(UploadedFile $file, string $directory): ?string
    {
        $allowed = ['image/png', 'image/jpeg', 'image/x-icon', 'image/vnd.microsoft.icon', 'image/webp'];

        if (! in_array($file->getMimeType(), $allowed, true) || $file->getSize() > self::MAX_BYTES) {
            return null;
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $extension = in_array($extension, ['png', 'jpg', 'jpeg', 'ico', 'webp'], true) ? $extension : 'png';

        $path = trim($directory, '/').'/'.Str::uuid().'.'.$extension;
        Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));

        return $path;
    }

    public function delete(?string $path, bool $withThumb = true): void
    {
        if (! $path) {
            return;
        }

        Storage::disk('public')->delete($path);

        if ($withThumb) {
            Storage::disk('public')->delete(preg_replace('/\.webp$/', '-thumb.webp', $path));
        }
    }

    private function createImage(UploadedFile $file): ?\GdImage
    {
        return match ($file->getMimeType()) {
            'image/jpeg' => @imagecreatefromjpeg($file->getRealPath()) ?: null,
            'image/png' => @imagecreatefrompng($file->getRealPath()) ?: null,
            'image/webp' => @imagecreatefromwebp($file->getRealPath()) ?: null,
            'image/avif' => function_exists('imagecreatefromavif')
                ? (@imagecreatefromavif($file->getRealPath()) ?: null)
                : null,
            default => null,
        };
    }

    private function resizeAndEncode(\GdImage $image, ?int $maxWidth): string
    {
        $width = imagesx($image);
        $height = imagesy($image);

        $targetWidth = $maxWidth !== null ? min($width, $maxWidth) : $width;
        $targetHeight = (int) round($height * ($targetWidth / $width));

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);

        // Preserve alpha so logo marks and avatars keep clean edges on dark
        // surfaces. JPEG sources have no alpha, so photos are unaffected.
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefill($canvas, 0, 0, $transparent);
        imagealphablending($canvas, true);

        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        ob_start();
        imagewebp($canvas, null, 82);
        $bytes = (string) ob_get_clean();

        imagedestroy($canvas);

        return $bytes;
    }
}
