<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProvinceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $companies = Province::get();
        return response()->json($companies);
    }

}
