<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\TweetController;


Route::post('/login', [AuthenticationController::class, 'authenticate']);
Route::post('/register', [AuthenticationController::class, 'register']);
Route::post('/validate-user-phone', [AuthenticationController::class, 'validateUserPhone']);
Route::post('/validate-user-email', [AuthenticationController::class, 'validateUserEmail']);


Route::middleware('custom_auth')->group(function () {
    Route::post('/tweet-list', [TweetController::class, 'getAllTweets']);
    Route::put('/tweets/{$id}', [TweetController::class, 'updateTweet']);
    Route::post('/tweets/{$id}/publish', [TweetController::class, 'publishTweet']);
    Route::get('/tweets/sync', [TweetController::class, 'syncLastTweetsWithRemote']);
});
