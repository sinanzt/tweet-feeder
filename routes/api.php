<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\TweetController;

Route::post(
    '/login',
    [AuthenticationController::class,'authenticate']
)->name('login');

Route::post(
    '/register',
    [AuthenticationController::class, 'register']
)->name('register');

Route::post(
    '/validate-phone',
    [AuthenticationController::class, 'validatePhone']
)->name('validate.phone');

Route::post(
    '/validate-email',
    [AuthenticationController::class, 'validateEmail']
)->name('validate.email');



Route::middleware('custom_auth')->prefix('tweets')->group(function () {
    Route::get(
        '',
        [TweetController::class, 'list']
    )->name('tweets.list');

    Route::put(
        '/{id}',
        [TweetController::class, 'update']
    )->name('tweets.update');

    Route::post(
        '/{id}/publish',
        [TweetController::class, 'publish']
    )->name('tweets.publish');

    Route::post(
        '/sync',
        [TweetController::class, 'syncWithRemote']
    )->name('tweets.sync');
});
