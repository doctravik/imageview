<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Jobs\SendActivationToken;
use App\Http\Controllers\Controller;

class ResendActivationToken extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');    
    }

    /**
     * Resend activation token for user.
     * 
     * @param  User   $user
     * @return \Illuminate\Http\Response
     */
    public function __invoke(User $user)
    {
        if ($user->isActive()) {
            return redirect('/home');
        }

        dispatch(new SendActivationToken($user));

        return back()->with('status', 'Activation token was resent to your email.');
    }
}
