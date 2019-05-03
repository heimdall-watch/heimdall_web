<?php

namespace App\HttpFoundation\File;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ApiUploadedFile extends UploadedFile
{
    public function __construct(string $base64Content, string $extension)
    {
        $filePath = tempnam(sys_get_temp_dir(), 'php');
        file_put_contents($filePath, base64_decode($base64Content));

        parent::__construct($filePath, "photo" . $extension, mime_content_type($filePath), null, true);
    }
}