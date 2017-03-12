<?php

namespace App\Http\Controllers;

use App\Photo;
use Illuminate\Http\Request;
use App\Http\Requests\StorePhotoRequest;

class PhotoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('store');
    }

    /**
     * Store photo in database.
     *  
     * @param  StorePhotoRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePhotoRequest $request)
    {
        Photo::upload(request('photos'));

        return back();
    }

    /**
     * Show photo.
     * @param  Photo  $photo
     * @return \Illuminate\Http\Response
     */
    public function show(Photo $photo)
    {
        $photos = Photo::all();

        $currentKey = $photos->search(function ($item, $key) use ($photo) {
            return $item->id === $photo->id;
        });

        $next = $photos->get($currentKey + 1);
        $prev = $photos->get($currentKey - 1);

        return view('photo.show', compact('photo', 'next', 'prev'));
    }
}
