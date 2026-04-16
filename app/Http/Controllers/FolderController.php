<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    public function index(): JsonResponse
    {
        $folders = Folder::query()
            ->withCount('workSamples')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return response()->json($folders);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $folder = Folder::query()->create($validated);

        return response()->json($folder, 201);
    }

    public function show(Folder $folder): JsonResponse
    {
        $folder->load('workSamples');

        return response()->json($folder);
    }

    public function update(Request $request, Folder $folder): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $folder->update($validated);

        return response()->json($folder->fresh('workSamples'));
    }

    public function destroy(Folder $folder): JsonResponse
    {
        $folder->delete();

        return response()->json(['message' => 'Folder deleted.']);
    }
}
