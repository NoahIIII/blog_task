<?php

namespace App\Http\Middleware;

use App\Http\Traits\ResponseHandler;
use Closure;
use Exception;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class UserAuthentication
{
    public function handle($request, Closure $next)
    {
        /*
        the token is sent in the cookies in a variable called 'token'
         */

        try {
            // make sure that the token valid
            Auth::authenticate();
        } catch (Exception $e) {
            if ($e instanceof TokenExpiredException) {
                // return response()->json(['error' => 'token expired'], 401);
            } else {
                return response()->json(['error' => __('messages.invalid-token')], 401);
            }
        }

        // get current user
        $user = Auth::user();

        if (!$user) {
            return ResponseHandler::errorResponse(__('messages.not-found'), 404);
        }

        // check status
        if ($user->status == false) {
            return response()->json(['error' => __('messages.deactivated-account')], 403);
        }

        // save user id to use later
        session(['user_id' => $user->id]);

        return $next($request);
    }
}
