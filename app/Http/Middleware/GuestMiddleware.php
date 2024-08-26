<?php

namespace App\Http\Middleware;

use App\Http\Traits\ResponseHandler;
use Closure;
use Exception;
use Laravel\Sanctum\PersonalAccessToken;

class GuestMiddleware
{
    public function handle($request, Closure $next)
    {
        /*
        the token is sent in the cookies in a variable called 'token'
         */

        // Retrieve the token from cookies
        $token = $request->cookie('token');
        if (!$token) {
            return $next($request);
        }
        // Find the token in the database
        $tokenModel = PersonalAccessToken::findToken($token);
        if (!$tokenModel) {
            return $next($request);
        }

        // save user id to use later
        return ResponseHandler::successResponse("You are logged in",null);
    }
}
