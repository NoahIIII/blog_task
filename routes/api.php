<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


// ---------------------------------------------------- Auth ----------------------------------------------------------
Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [UserAuthController::class, 'login'])->name('login')->middleware('guest_middleware');
    Route::post('signup', [UserAuthController::class, 'signup'])->middleware('guest_middleware');
    Route::post('/logout', [UserAuthController::class, 'logout'])->middleware('user_authentication');
    Route::get('/refresh-token', [UserAuthController::class, 'refreshToken'])->middleware('user_authentication');
    Route::get('/is-authenticated', [UserAuthController::class, 'isAuthenticated']);
});

//-------------------------------------------- Posts ------------------------------------------------------------------
Route::group(['prefix' => 'posts'], function () {
    Route::get('/', [PostController::class, 'getAllPosts']);
    Route::get('/{postId}', [PostController::class, 'getPost']);
    Route::post('/add', [PostController::class, 'addPost'])->middleware('user_authentication');
    Route::patch('/{postId}/update', [PostController::class, 'updatePost'])->middleware('user_authentication');
    Route::delete('/delete', [PostController::class, 'bulkDeletePosts'])->middleware('user_authentication');

    //----------------------------- comments ----------------------------------
    Route::post('/{postId}/comments/add', [CommentController::class, 'addComment'])->middleware('user_authentication');
    Route::delete('/comments/{commentId}/delete', [CommentController::class, 'deleteComment'])->middleware('user_authentication');
});
