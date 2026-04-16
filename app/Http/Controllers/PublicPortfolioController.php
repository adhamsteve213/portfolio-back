<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\JsonResponse;

class PublicPortfolioController extends Controller
{
    public function index(): JsonResponse
    {
        $folders = Folder::query()
            ->with(['workSamples'])
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return response()->json($folders);
    }

    public function show(Folder $folder): JsonResponse
    {
        $folder->load('workSamples');

        return response()->json($folder);
    }
}
