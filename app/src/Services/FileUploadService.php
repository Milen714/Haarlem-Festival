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

        $mimeType = $this->getMimeType($file['tmp_name']);
        if ($mimeType === false) {
            return $this->error('Failed to detect file type');
        }

        $shouldConvertToWebP = in_array($mimeType, ['image/jpeg', 'image/jpg', 'image/png'], true);

        $filename = $this->generateFilename($file['name'], $shouldConvertToWebP ? 'webp' : null);
        $targetDir = self::UPLOAD_DIR . $category . '/';
        $relativePath = '/Assets/' . $category . '/' . $filename;

        if (!$this->ensureDirectoryExists($targetDir)) {
            return $this->error('Failed to create upload directory');
        }

        $destinationPath = $targetDir . $filename;

        if ($shouldConvertToWebP) {
            $conversionResult = $this->convertToWebP($file['tmp_name'], $destinationPath, $mimeType);
            if (!$conversionResult['success']) {
                return $conversionResult;
            }
        } elseif (!move_uploaded_file($file['tmp_name'], $destinationPath)) {
            return $this->error('Failed to save file');
        }

        return ['success' => true, 'file_path' => $relativePath, 'error' => null];
    }

    private function convertToWebP(string $sourcePath, string $destinationPath, string $mimeType): array
    {
        try {
            $image = null;

            switch ($mimeType) {
                case 'image/jpeg':
                case 'image/jpg':
                    $image = imagecreatefromjpeg($sourcePath);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($sourcePath);
                    break;
                default:
                    return $this->error('Unsupported file type');
            }

            if (!$image) {
                return $this->error('Failed to create image resource');
            }

            if (!imagewebp($image, $destinationPath)) {
                return $this->error('Failed to convert to WebP');
            }

            return ['success' => true, 'file_path' => null, 'error' => null];
        } catch (\Exception $e) {
            return $this->error('Conversion error: ' . $e->getMessage());
        }
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
        $mimeType = $this->getMimeType($tmpPath);
        if ($mimeType === false) {
            return false;
        }
        return in_array($mimeType, self::ALLOWED_TYPES);
    }

    private function getMimeType(string $path): string|false
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        return finfo_file($finfo, $path);
    }

    private function generateFilename(string $originalName, ?string $forcedExtension = null): string
    {
        $extension = $forcedExtension ?? strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
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