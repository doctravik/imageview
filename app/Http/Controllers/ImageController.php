<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function show($path)
    {
        $image = Image::cache(function($image) use ($path) {
            $image->make(Storage::url($path));
        }, 1);

        return response($image, 200, ['Content-Type' => 'image/jpeg']);        
    }
}
