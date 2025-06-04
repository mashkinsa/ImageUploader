<?php

namespace Mashkinsa\ImageUploader;

use Mashkinsa\ImageUploader\Exceptions\ImageUploadException;

class ImageUploader
{
    private array $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp'
    ];

    private int $maxFileSize = 5 * 1024 * 1024; // 5MB

    public function __construct(array $config = [])
    {
        $this->configure($config);
    }

    public function upload(array $file, string $uploadDir): string
    {
        $this->validate($file);

        $filename = $this->generateFilename($file['name']);
        $targetPath = rtrim($uploadDir, '/') . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new ImageUploadException('Failed to move uploaded file');
        }

        return $filename;
    }

    private function validate(array $file): void
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new ImageUploadException('File upload error: ' . $file['error']);
        }

        if ($file['size'] > $this->maxFileSize) {
            throw new ImageUploadException('File size exceeds maximum allowed');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        if (!in_array($mime, $this->allowedMimeTypes)) {
            throw new ImageUploadException('Invalid file type');
        }
    }

    private function generateFilename(string $originalName): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        return uniqid() . '.' . $extension;
    }

    private function configure(array $config): void
    {
        if (isset($config['allowed_types'])) {
            $this->allowedMimeTypes = (array)$config['allowed_types'];
        }

        if (isset($config['max_size'])) {
            $this->maxFileSize = (int)$config['max_size'];
        }
    }
}