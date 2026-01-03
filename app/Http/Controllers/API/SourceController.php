<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Source;
use App\Services\Remappers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $sources = Source::get();

        $remapper = new Remappers();
        $remapSources = $remapper->remapSources($sources);

        return response()->json($remapSources);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|unique:sources,name',
            'is_active' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {
            $source = Source::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'is_active' => $request->is_active,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'source created successfully.',
                'data' => $source
            ], 201);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create Source.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|unique:sources,name,' . $id,
            'is_active' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {
            $source = Source::findOrFail($id);

            $source->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'is_active' => $request->is_active,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'source created successfully.',
                'data' => $source
            ], 201);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create Source.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        $source = Source::findOrFail($id);
        $source->delete();

        return response()->json([
            'message' => 'source deleted successfully.'
        ]);
    }
}
