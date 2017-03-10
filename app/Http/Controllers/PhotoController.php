<?php

namespace App\Http\Controllers;

use App\Photo;
use Illuminate\Http\Request;
use App\Http\Requests\StorePhotoRequest;

class PhotoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');    
    }

    public function store(StorePhotoRequest $request)
    {
        Photo::upload(request('photos'));

        return back();
    }
}
