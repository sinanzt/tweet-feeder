<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\TweetController;


Route::post('/login', [AuthenticationController::class, 'login']);;
Route::post('/reset-password', [AuthenticationController::class, 'resetPassword']);
Route::post('/sign-up', [AuthenticationController::class, 'signUp']);
Route::post('/validate-user-phone', [AuthenticationController::class, 'validateUserPhone']);
Route::post('/validate-user-email', [AuthenticationController::class, 'validateUserEmail']);

Route::get('/tweets', [TweetController::class, 'getAllTweets']);
Route::post('/tweets/sync', [TweetController::class, 'syncLastTweetsWithRemote']);
