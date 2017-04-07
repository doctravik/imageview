<?php

namespace App\Http\Controllers\Webapi;

use App\Album;
use App\Photo;
use App\Thumbnail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\PhotoTransformer;
use App\Http\Requests\StorePhotoRequest;

class PhotoController extends Controller
{
    public function index(Album $album)
    {
        $this->authorize('index', [Photo::class, $album]);

        $photos = $album->photos;

        return fractal()
            ->collection($photos)
            ->transformWith(new PhotoTransformer())
            ->toArray();
    }

    /**
     * Store photo in database.
     *  
     * @param  StorePhotoRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePhotoRequest $request, Album $album)
    {
        $this->authorize('store', [Photo::class, $album]);

        if(auth()->user()->isOutOfLimit()) {
            return response()->json(['photo' => ['User cannot upload more than five photos']], 422);
        }

        $photo = Photo::upload(request('photo'), $album);
        $thumbnail = Thumbnail::make($photo->path)->resize()->save();

        return fractal()
            ->item($photo)
            ->parseIncludes('album')
            ->transformWith(new PhotoTransformer())
            ->toArray();
    }
}
