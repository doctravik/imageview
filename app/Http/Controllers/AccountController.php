<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function confirm()
    {   
        $user = auth()->user();

        return view('account.confirm', compact('user'));
    }
}
