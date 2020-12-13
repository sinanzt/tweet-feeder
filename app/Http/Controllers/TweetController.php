<?php

namespace App\Http\Controllers;

use App\Models\Tweet;
use Illuminate\Http\Request;


class TweetController extends Controller
{
    public function getAllTweets() {
        $tweets = Tweet::orderBy('published_at', 'desc')->with('user')->paginate(20);
        return response()->json($tweets,200);
    }

    public function syncLastTweetsWithRemote(Request $request) {

    }

    public function updateTweet(Request $request) {

    }

    public function publishTweet(Request $request) {

    }

    private function getLastTweetsFromRemote(string $username, $tweetCount = 20) {
        // TODO: Twitter api ye bağlan.

    }

    private function mapTweets(array $tweets): array {
        return array();
    }

}