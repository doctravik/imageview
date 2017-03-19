<?php

namespace App\Http\Controllers;

use App\Album;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    /**
     * Show photos from album.
     * 
     * @param  Album  $album
     * @return \Illuminate\Http\Response
     */
    public function show(Album $album)
    {
        $photos = $album->photos;
        
        return view('album.front.show', compact('album', 'photos'));
    }
}
