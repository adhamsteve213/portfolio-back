<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MediaController extends Controller
{
    public function show(string $path): BinaryFileResponse|Response
    {
        $normalizedPath = ltrim($path, '/');

        if ($normalizedPath === '' || str_contains($normalizedPath, '..')) {
            abort(404);
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($normalizedPath)) {
            abort(404);
        }

        $absolutePath = $disk->path($normalizedPath);

        $raw = @file_get_contents($absolutePath);

        if ($raw !== false) {
            $decoded = @imagecreatefromstring($raw);

            if ($decoded !== false) {
                $jpeg = $this->encodeJpeg($decoded);

                if ($jpeg !== null) {
                    return response($jpeg, 200, [
                        'Content-Type' => 'image/jpeg',
                        'Cache-Control' => 'public, max-age=31536000, immutable',
                    ]);
                }
            }
        }

        return response()->file($absolutePath);
    }

    private function encodeJpeg(\GdImage $source): ?string
    {
        $width = imagesx($source);
        $height = imagesy($source);

        $canvas = imagecreatetruecolor($width, $height);

        if ($canvas === false) {
            imagedestroy($source);

            return null;
        }

        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefilledrectangle($canvas, 0, 0, $width, $height, $white);
        imagecopy($canvas, $source, 0, 0, 0, 0, $width, $height);

        imagedestroy($source);

        ob_start();
        $success = imagejpeg($canvas, null, 90);
        $binary = ob_get_clean();

        imagedestroy($canvas);

        if (! $success || $binary === false) {
            return null;
        }

        return $binary;
    }
}
