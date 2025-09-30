<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class FileUploadHelper
{
    public static function uploadImage(UploadedFile $file, string $prefix = 'file', string $folder = 'uploads/custom-images'): string
    {
        $filename = $prefix . '-' . date('Y-m-d-h-i-s') . '-' . rand(999, 9999) . '.' . $file->getClientOriginalExtension();
        $path = $folder . '/' . $filename;

        $file->move(public_path($folder), $filename);

        return $path;
    }

    public static function deleteImage(string $path): bool
    {
        $fullPath = public_path($path);

        if (File::exists($fullPath)) {
            return File::delete($fullPath);
        }

        return false;
    }
}
