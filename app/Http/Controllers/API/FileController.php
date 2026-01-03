<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class FileController extends Controller
{
    public function download(Request $request, $path)
    {
        $path = urldecode($path);

        abort_if(!Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->download($path);
    }

    public function stream(Request $request, $path)
    {
        $path = urldecode($path);

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found');
        }

        $fullPath = Storage::disk('public')->path($path);
        $mimeType = mime_content_type($fullPath);

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline',
        ]);
    }
}
