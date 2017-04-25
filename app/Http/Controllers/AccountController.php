<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show confirm notification.
     * 
     * @return \Illuminate\Http\Response
     */
    public function confirm()
    {   
        $user = auth()->user();

        return view('account.confirm', compact('user'));
    }
}
