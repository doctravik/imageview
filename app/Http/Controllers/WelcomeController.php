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
        $albums = Album::with('photos')->get();

        return view('welcome', compact('albums'));
    }
}
