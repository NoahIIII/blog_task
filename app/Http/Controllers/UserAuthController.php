<?php

namespace App\Http\Controllers;

use App\Http\Traits\ResponseHandler;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Exception;
use Laravel\Sanctum\PersonalAccessToken;

class UserAuthController extends Controller
{
    // ------------------------------------------------------------ login ------------------------------------------------------------
    public function login(Request $request)
    {
        // make validation rules
        $validator = Validator::make($request->all(), [
            'email' => 'required|regex:/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/|email',
            'password' => 'required|string|min:8|max:50|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{8,50}$/',
        ]);

        // validate received data
        if ($validator->fails()) {
            return ResponseHandler::errorResponse($validator->errors()->first(), 400);
        }

        // get data from request
        $email = $request->get('email');
        $password = $request->get('password');

        // get user
        $user = User::where('email', $email)->first();

        if (!$user) {
            return ResponseHandler::errorResponse(__('messages.email-wrong'), 401);
        }

        // check password
        if (!Hash::check($password, $user->password)) {
            return ResponseHandler::errorResponse(__('messages.password-wrong'), 401);
        }

        // generate token
        $token = auth()->login($user);

        // return response & set the token in the cookies
        return response()
            ->json(['msg' => __("messages.welcome", ['name' => $user->name])], 200)
            ->withCookie(cookie('token', $token, env('JWT_REFRESH_TTL'), '/', null, true, true, false, 'None'));
    }

    // ------------------------------------------------------------ signup ------------------------------------------------------------
    public function signup(Request $request)
    {

        // make validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:50',
            'email' => 'required|unique:users,email|regex:/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/',
            'password' => 'required|string|min:8|max:50|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{8,50}$/|confirmed',
        ]);

        // Validate received data
        if ($validator->fails()) {
            return ResponseHandler::errorResponse($validator->errors()->first(), 400);
        }

        // Create a new User instance
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));

        // Save the new User record
        $user->save();

        // generate token
        $token = auth()->login($user);

        // return response & set the token in the cookies
        return response()
            ->json(['msg' => __("messages.welcome", ['name' => $user->name])], 200)
            ->withCookie(cookie('token', $token, env('JWT_REFRESH_TTL'), '/', null, true, true, false, 'None'));
    }

    // ------------------------------------------------------------ refresh token ------------------------------------------------------------
    public function refreshToken(Request $request)
    {
        $newToken = null;
        try {
            // refresh token
            $newToken = Auth::refresh();
        } catch (Exception $e) {
            return ResponseHandler::errorResponse(__('messages.invalid-token'), 401);
        }

        return response()
            ->json(["token" => $newToken], 200)
            ->withCookie(cookie('token', $newToken, env('JWT_REFRESH_TTL'), '/', null, true, true, false, 'None'));
    }

    // ------------------------------------------------------------ logout ------------------------------------------------------------
    public function logout(Request $request)
    {

        try {
            // invalidate current token if it is valid
            Auth::logout();
        } catch (Exception $e) {
        }

        return ResponseHandler::successResponse(__('messages.logout'), null, 200);
    }
}
