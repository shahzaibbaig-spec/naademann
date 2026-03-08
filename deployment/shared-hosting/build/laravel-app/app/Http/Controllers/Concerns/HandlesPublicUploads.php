<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait HandlesPublicUploads
{
    protected function storePublicUpload(?UploadedFile $file, string $folder, ?string $currentPath = null): ?string
    {
        if (!$file) {
            return $currentPath;
        }

        $filename = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs("uploads/{$folder}", $filename, 'public');

        return $path ? '/storage/'.ltrim($path, '/') : $currentPath;
    }
}
