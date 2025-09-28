<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Laravel\Facades\Image;

class ImageResizeService
{

    public function resizeAndStore(UploadedFile $image, string $folder = 'servers'): string
    {
        $path = "{$folder}";
        $filename = time() . '.' . $image->getClientOriginalExtension();
        $image = Image::read($image)->resize('300', '300', function ($c) {
            $c->aspectRatio();
            $c->upsize();
        });

        Storage::disk('public')->makeDirectory($path);

        $fullPath = "{$path}/{$filename}";
        Storage::disk('public')->put($fullPath, (string) $image->encode());

        return $fullPath;
    }

    public function delete(string $path): void
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}