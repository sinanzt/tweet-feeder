<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\TweetController;


Route::post('/login', [AuthenticationController::class, 'authenticate'])->name('login');
Route::post('/register', [AuthenticationController::class, 'register'])->name('register');
Route::post('/validate-user-phone', [AuthenticationController::class, 'validateUserPhone'])->name('validate.user.phone');
Route::post('/validate-user-email', [AuthenticationController::class, 'validateUserEmail'])->name('validate.user.email');


Route::middleware('custom_auth')->group(function () {
    Route::post('/tweet-list', [TweetController::class, 'getAllTweets'])->name('list.tweets');
    Route::put('/tweets/{id}', [TweetController::class, 'updateTweet'])->name('update.tweet');
    Route::post('/tweets/{id}/publish', [TweetController::class, 'publishTweet'])->name('publish.tweet');
    Route::get('/tweets/sync', [TweetController::class, 'syncLastTweetsWithRemote'])->name('sync.tweets');
});
