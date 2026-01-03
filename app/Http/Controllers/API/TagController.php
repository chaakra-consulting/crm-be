<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Services\Remappers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index(): JsonResponse
    {
        $tags = Tag::latest()->get();

        $remapper = new Remappers();
        $remapTags = $remapper->remapTags($tags);

        return response()->json($remapTags);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|unique:tags,name',
            'color' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $tag = Tag::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'color' => $request->color,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'tag created successfully.',
                'data' => $tag
            ], 201);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create tag.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|unique:tags,name,' . $id,
            'color' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $tag = Tag::findOrFail($id);

            $tag->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'color' => $request->color,
                'is_active' => $request->is_active,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'tag created successfully.',
                'data' => $tag
            ], 201);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create tag.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        $tag = Tag::findOrFail($id);
        $tag->delete();

        return response()->json([
            'message' => 'tag deleted successfully.'
        ]);
    }
}
