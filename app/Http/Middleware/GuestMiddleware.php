<?php

namespace App\Http\Middleware;

use App\Http\Traits\ResponseHandler;
use Closure;
use Illuminate\Support\Facades\Auth;

class GuestMiddleware
{
    public function handle($request, Closure $next)
    {
        /*
        the token is sent in the cookies in a variable called 'token'
         */

        // get the current logged in user
        $user = Auth::user();
        if ($user) {
            return ResponseHandler::successResponse("You are already logged in, logout first if you want to switch accounts", null);
        }

        return $next($request);
    }
}
