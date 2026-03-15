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

    protected function effectiveUploadLimitKilobytes(int $applicationLimitKilobytes): int
    {
        $phpLimitKilobytes = $this->phpUploadLimitKilobytes();

        if ($phpLimitKilobytes === null) {
            return $applicationLimitKilobytes;
        }

        return max(1, min($applicationLimitKilobytes, $phpLimitKilobytes));
    }

    protected function formatKilobytes(int $kilobytes): string
    {
        if ($kilobytes >= 1024) {
            $megabytes = $kilobytes / 1024;
            $formatted = fmod($megabytes, 1.0) === 0.0
                ? (string) (int) $megabytes
                : number_format($megabytes, 1);

            return "{$formatted} MB";
        }

        return "{$kilobytes} KB";
    }

    protected function uploadFailedValidationMessage(string $fieldLabel, int $limitKilobytes): string
    {
        $limit = $this->formatKilobytes($limitKilobytes);

        return "The {$fieldLabel} exceeds the server upload limit ({$limit}). "
            ."Increase upload_max_filesize and post_max_size in PHP settings or upload a smaller file.";
    }

    private function phpUploadLimitKilobytes(): ?int
    {
        $uploadMax = $this->iniSizeToKilobytes((string) ini_get('upload_max_filesize'));
        $postMax = $this->iniSizeToKilobytes((string) ini_get('post_max_size'));
        $limits = array_values(array_filter([$uploadMax, $postMax], static fn (?int $value): bool => $value !== null));

        if ($limits === []) {
            return null;
        }

        return min($limits);
    }

    private function iniSizeToKilobytes(string $value): ?int
    {
        $normalized = trim($value);

        if ($normalized === '' || $normalized === '-1') {
            return null;
        }

        $unit = strtolower(substr($normalized, -1));
        $number = (float) $normalized;

        $bytes = match ($unit) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => (float) $normalized,
        };

        if ($bytes <= 0) {
            return null;
        }

        return (int) floor($bytes / 1024);
    }
}
