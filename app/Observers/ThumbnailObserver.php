<?php

namespace App\Observers;

use App\Thumbnail;
use Illuminate\Support\Facades\Storage;

class ThumbnailObserver
{
    public function creating(Thumbnail $thumbnail)
    {
        $thumbnail->slug = str_slug(pathinfo($thumbnail->path, PATHINFO_FILENAME));
    }

    public function deleting(Thumbnail $thumbnail)
    {
        Storage::delete($thumbnail->path);
    }
}