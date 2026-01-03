<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {

        $companies = City::filterProvince($request->province_id)
            ->latest()
            ->get();

        return response()->json($companies);
    }
}
