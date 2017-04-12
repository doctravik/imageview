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
    /**
     * View all photos from album.
     * 
     * @param  Album  $album
     * @return array
     */
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
     * @return Array
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

    /**
     * Delete photo from database.
     * 
     * @param  Album  $album
     * @param  Photo  $photo
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Album $album, Photo $photo) 
    {
        $this->authorize('destroy', [$photo, $album]);

        $photo->delete();

        return response()->json(['message' => 'success'], 200);       
    }
}
