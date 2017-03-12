<?php

namespace App\Http\Controllers;

use App\User;
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
        $users = User::with('photos')->get();

        return view('welcome', compact('users'));
    }
}
