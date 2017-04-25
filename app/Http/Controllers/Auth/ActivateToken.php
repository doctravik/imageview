<?php

namespace App\Http\Controllers\Auth;

use App\ActivationToken;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActivateToken extends Controller
{
    /**
     * Activate token.
     * 
     * @param  string $token
     * @return \Illuminate\Http\Response
     */
    public function __invoke($token)
    {
        $token = ActivationToken::whereToken($token)->firstOrFail();

        $token->user->activate();

        $token->delete();

        \Auth::login($token->user);

        return redirect('/home');
    }
}
