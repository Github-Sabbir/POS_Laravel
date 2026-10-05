<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MediaController extends Controller
{
    public function file(string $path): Response
    {
        $disk = Storage::disk('public');
        if (!$disk->exists($path)) {
            abort(404);
        }
        $fullPath = $disk->path($path);
        return response()->file($fullPath, [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
