<?php
declare(strict_types=1);

namespace App\Services;

use Intervention\Image\ImageManagerStatic as Image;

class ImageService
{
    /**
     * Optimize an image to WebP with max dimension constraint.
     * Falls back to copying source to destination on failure.
     */
    public static function optimizeToWebp(string $src, string $dest, int $maxDim = 1600, int $quality = 80): bool
    {
        try {
            // Some envs may not have GD/Imagick; Intervention handles selection
            $img = Image::make($src);
            $w = $img->width();
            $h = $img->height();
            if ($w > $maxDim || $h > $maxDim) {
                $img->resize($maxDim, $maxDim, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            // Ensure directory exists
            $dir = dirname($dest);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $img->encode('webp', $quality)->save($dest);
            return true;
        } catch (\Throwable $e) {
            // Fallback: copy original
            if (!is_dir(dirname($dest))) {
                mkdir(dirname($dest), 0755, true);
            }
            return copy($src, $dest);
        }
    }
}
