<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\WorkSample;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WorkSampleController extends Controller
{
    public function store(Request $request, Folder $folder): JsonResponse
    {
        $validated = $request->validate([
            'project_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ]);

        $path = $request->file('image')->store('work-samples', 'public');

        $sample = $folder->workSamples()->create([
            'project_name' => $validated['project_name'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'image_path' => $path,
        ]);

        return response()->json($sample, 201);
    }

    public function update(Request $request, WorkSample $workSample): JsonResponse
    {
        $validated = $request->validate([
            'project_name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'image' => ['sometimes', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($workSample->image_path);
            $validated['image_path'] = $request->file('image')->store('work-samples', 'public');
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
}
