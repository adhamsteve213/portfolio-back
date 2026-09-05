<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\WorkSample;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class WorkSampleController extends Controller
{
    public function store(Request $request, Folder $folder): JsonResponse
    {
        $validated = $request->validate([
            'project_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'image' => ['required_without:images', 'file', 'mimetypes:image/*'],
            'images' => ['required_without:image', 'array'],
            'images.*' => ['file', 'mimetypes:image/*'],
        ]);

        $uploadedImages = [];

        if ($request->hasFile('image')) {
            $uploadedImages[] = $request->file('image');
        }

        if ($request->hasFile('images')) {
            $uploadedImages = array_merge($uploadedImages, $request->file('images'));
        }

        $samples = [];
        $sortOrder = $validated['sort_order'] ?? 0;

        foreach ($uploadedImages as $index => $uploadedImage) {
            $storedImage = $this->storeWithOriginalFileName($uploadedImage);

            $samples[] = $folder->workSamples()->create([
                'project_name' => $validated['project_name'],
                'description' => $validated['description'] ?? null,
                'sort_order' => $sortOrder + $index,
                'image_path' => $storedImage['path'],
                'image_data' => $storedImage['data'],
                'image_mime' => $storedImage['mime'],
            ]);
        }

        if (count($samples) === 1) {
            return response()->json($samples[0], 201);
        }

        return response()->json($samples, 201);
    }

    public function update(Request $request, WorkSample $workSample): JsonResponse
    {
        $validated = $request->validate([
            'project_name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'image' => ['sometimes', 'file', 'mimetypes:image/*'],
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($workSample->image_path);
            $storedImage = $this->storeWithOriginalFileName($request->file('image'));
            $validated['image_path'] = $storedImage['path'];
            $validated['image_data'] = $storedImage['data'];
            $validated['image_mime'] = $storedImage['mime'];
        }

        $workSample->update($validated);

        return response()->json($workSample->fresh());
    }

    public function destroy(WorkSample $workSample): JsonResponse
    {
        Storage::disk('public')->delete($workSample->image_path);
        $workSample->delete();

        return response()->json(['message' => 'Work sample deleted.']);
    }

    private function storeWithOriginalFileName(UploadedFile $file): array
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $safeName = Str::of($originalName)->trim()->slug('_')->value();

        if ($safeName === '') {
            $safeName = 'image';
        }

        $contents = @file_get_contents($file->getRealPath());

        if ($contents !== false) {
            $decoded = @imagecreatefromstring($contents);

            if ($decoded !== false) {
                $jpegPath = $this->getUniquePath("work-samples/{$safeName}", 'jpg');
                $jpegBinary = $this->encodeJpeg($decoded);

                if ($jpegBinary !== null) {
                    Storage::disk('public')->put($jpegPath, $jpegBinary);

                    return [
                        'path' => $jpegPath,
                        'data' => base64_encode($jpegBinary),
                        'mime' => 'image/jpeg',
                    ];
                }
            }
        }

        $relativePath = $this->getUniquePath("work-samples/{$safeName}", $extension);

        Storage::disk('public')->putFileAs('work-samples', $file, basename($relativePath));

        $raw = @file_get_contents($file->getRealPath());
        $mime = $file->getMimeType() ?: 'application/octet-stream';

        return [
            'path' => $relativePath,
            'data' => $raw !== false ? base64_encode($raw) : null,
            'mime' => $mime,
        ];
    }

    private function getUniquePath(string $basePath, string $extension): string
    {
        $relativePath = "{$basePath}.{$extension}";
        $counter = 1;

        while (Storage::disk('public')->exists($relativePath)) {
            $relativePath = "{$basePath}_{$counter}.{$extension}";
            $counter++;
        }

        return $relativePath;
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
