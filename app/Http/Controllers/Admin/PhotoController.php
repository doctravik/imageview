<?php

namespace App\Http\Controllers\Admin;

use App\Album;
use App\Photo;
use App\Thumbnail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePhotosRequest;

class PhotoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');    
    }

    /**
     * Store photo in database.
     *  
     * @param  StorePhotosRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePhotosRequest $request, Album $album)
    {
        $this->authorize('store', [Photo::class, $album]);

        $file = request('photos');
        foreach (request('photos') as $file) {
            $photo = Photo::upload($file, $album);
            $thumbnail = Thumbnail::make($photo->path)->resize()->save();
        }

        return back();
    }
}
