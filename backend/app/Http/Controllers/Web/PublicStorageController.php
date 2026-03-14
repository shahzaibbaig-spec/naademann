<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class PublicStorageController extends Controller
{
    public function __invoke(string $path): BinaryFileResponse
    {
        $normalizedPath = trim($path, '/');

        abort_if(
            $normalizedPath === '' || str_contains($normalizedPath, '..'),
            Response::HTTP_NOT_FOUND
        );

        $disk = Storage::disk('public');

        abort_unless($disk->exists($normalizedPath), Response::HTTP_NOT_FOUND);

        return response()->file($disk->path($normalizedPath), [
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}
