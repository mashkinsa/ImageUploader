<?php

namespace Maskinsa\ImageUploader\Traits;

use Maskinsa\ImageUploader\ImageUploader;

trait HasImageUploads
{
    public function uploadImage(array $file, string $fieldName, string $storagePath): void
    {
        $uploader = new ImageUploader();
        $filename = $uploader->upload($file, $storagePath);
        $this->{$fieldName} = $filename;
    }
}