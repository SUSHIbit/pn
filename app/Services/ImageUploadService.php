<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class ImageUploadService
{
    /**
     * Upload and save an image file
     */
    public function uploadImage(UploadedFile $file, string $directory, ?int $userId = null): string
    {
        $userId = $userId ?? Auth::id();
        
        // Generate unique filename
        $filename = time() . '_' . $userId . '.' . $file->getClientOriginalExtension();
        
        // Ensure directory exists
        $uploadPath = public_path('uploads/' . $directory);
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        // Move file to uploads directory
        $file->move($uploadPath, $filename);
        
        return $filename;
    }

    /**
     * Delete an image file
     */
    public function deleteImage(string $filename, string $directory): bool
    {
        $filepath = public_path('uploads/' . $directory . '/' . $filename);
        
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        
        return false;
    }

    /**
     * Get image URL
     */
    public function getImageUrl(string $filename, string $directory): string
    {
        return asset('uploads/' . $directory . '/' . $filename);
    }

    /**
     * Validate image file
     */
    public function validateImage(UploadedFile $file, int $maxSize = 2048): array
    {
        $errors = [];
        
        // Check file type
        $allowedTypes = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
        if (!in_array(strtolower($file->getClientOriginalExtension()), $allowedTypes)) {
            $errors[] = 'Image must be a JPEG, PNG, GIF, or WebP file.';
        }
        
        // Check file size (in KB)
        if ($file->getSize() > ($maxSize * 1024)) {
            $errors[] = "Image must be smaller than {$maxSize}KB.";
        }
        
        return $errors;
    }
}