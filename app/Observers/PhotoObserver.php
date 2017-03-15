<?php

namespace App\Observers;

use App\Photo;
use Illuminate\Support\Facades\Storage;

class PhotoObserver
{
    public function creating(Photo $photo)
    {
        $photo->slug = str_slug(pathinfo($photo->path, PATHINFO_FILENAME));
    }

    public function deleting(Photo $photo)
    {
        Storage::delete($photo->path);

        if($photo->thumbnails()->count() > 0) {
            $photo->thumbnails->each(function($thumbnail) {
                Storage::delete($thumbnail->path);                
            });
        }
    }
}