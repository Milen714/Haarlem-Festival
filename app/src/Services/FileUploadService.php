<?php

namespace App\Services;

class FileUploadService
{
    private const UPLOAD_DIR = __DIR__ . '/../../public/Assets/';
    private const MAX_FILE_SIZE = 5242880; // 5MB
    private const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];

    public function upload(array $file, string $category): array
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return $this->error('No file uploaded');
        }

        if ($file['size'] > self::MAX_FILE_SIZE) {
            return $this->error('File too large (max 5MB)');
        }

        if (!$this->isValidFileType($file['tmp_name'])) {
            return $this->error('Invalid file type. Only JPG, PNG, and WebP allowed');
        }

        $filename = $this->generateFilename($file['name']);
        $targetDir = self::UPLOAD_DIR . $category . '/';
        $relativePath = '/Assets/' . $category . '/' . $filename;

        if (!$this->ensureDirectoryExists($targetDir)) {
            return $this->error('Failed to create upload directory');
        }

        if (!move_uploaded_file($file['tmp_name'], $targetDir . $filename)) {
            return $this->error('Failed to save file');
        }

        return ['success' => true, 'file_path' => $relativePath, 'error' => null];
    }

    public function delete(string $filePath): bool
    {
        $fullPath = __DIR__ . '/../../public' . $filePath;

        if (file_exists($fullPath) && is_file($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }

    private function isValidFileType(string $tmpPath): bool
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $tmpPath);
        // Removed finfo_close() - deprecated in PHP 8.5
        // The finfo resource is automatically freed

        return in_array($mimeType, self::ALLOWED_TYPES);
    }

    private function generateFilename(string $originalName): string
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        return uniqid() . '_' . time() . '.' . $extension;
    }

    private function ensureDirectoryExists(string $dir): bool
    {
        if (!is_dir($dir)) {
            return mkdir($dir, 0755, true);
        }
        return true;
    }

    private function error(string $message): array
    {
        return ['success' => false, 'file_path' => null, 'error' => $message];
    }
}