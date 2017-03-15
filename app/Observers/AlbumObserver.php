<?php

namespace App\Observers;

use App\Album;

class AlbumObserver
{
    public function creating(Album $album)
    {
        $album->slug = str_slug($album->name);
    }

    public function updating(Album $album)
    {   
        $album->slug = str_slug($album->name);
    }
}