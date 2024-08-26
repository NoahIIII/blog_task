<?php

namespace App\Http\Middleware;

use App\Http\Traits\ResponseHandler;
use Closure;
use Exception;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class UserAuthentication
{
    public function handle($request, Closure $next)
    {
        /*
        the token is sent in the cookies in a variable called 'token'
         */

        // Retrieve the token from cookies
        $token = $request->cookie('token');
        if (!$token) {
            return ResponseHandler::errorResponse(__('messages.invalid-token'), 401);
        }
        // Find the token in the database
        $tokenModel = PersonalAccessToken::findToken($token);
        if (!$tokenModel) {
            return ResponseHandler::errorResponse(__('messages.invalid-token'), 401);
        }

        // save user id to use later
        session(['user_id' => $tokenModel->tokenable->id]);

        return $next($request);
    }
}
