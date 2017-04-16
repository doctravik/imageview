<?php

namespace App\Http\Controllers;

use App\Album;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    /**
     * Show welcome page.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $albums = Album::join('users', 'users.id', '=', 'albums.user_id')
            ->select('albums.*', 'users.name as username')
            ->with(['avatar', 'publicPhotos'])
            ->latest()->paginate(1);
        
        return view('welcome', compact('albums'));
    }
}
