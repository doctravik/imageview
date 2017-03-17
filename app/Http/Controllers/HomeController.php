<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show user dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $albums = auth()->user()->albums; 

        return view('home.index', compact('albums'));
    }
}
