<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MediaController extends Controller
{
    public function file(string $path): Response
    {
        $disk = Storage::disk('public');
        $path = ltrim(urldecode($path), '/');
        $candidates = array_values(array_unique([
            $path,
            preg_replace('#^(public/|storage/)#', '', $path),
        ]));

        foreach ($candidates as $candidate) {
            if ($candidate && $disk->exists($candidate)) {
                return response()->file($disk->path($candidate), [
                    'Cache-Control' => 'public, max-age=86400',
                ]);
            }
        }

        abort(404);
    }
}
