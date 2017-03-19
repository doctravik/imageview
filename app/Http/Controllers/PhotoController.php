<?php

namespace App\Http\Controllers;

use App\Photo;
use App\Thumbnail;
use App\PhotoToThumbnail;
use Illuminate\Http\Request;
use App\Http\Requests\StorePhotoRequest;

class PhotoController extends Controller
{
    /**
     * Show photo.
     * 
     * @param  Photo  $photo
     * @return \Illuminate\Http\Response
     */
    public function show(Photo $photo)
    {
        $photos = $photo->album->photos;

        $currentKey = $photos->search(function ($item, $key) use ($photo) {
            return $item->id === $photo->id;
        });

        $next = $photos->get($currentKey + 1);
        $prev = $photos->get($currentKey - 1);

        return view('photo.show', compact('photo', 'next', 'prev'));
    }
}
