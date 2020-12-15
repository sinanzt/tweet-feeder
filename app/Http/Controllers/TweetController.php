<?php

namespace App\Http\Controllers;

use App\Models\Tweet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TweetController extends Controller
{
    public function list(Request $request)
    {
        $twitterUsername = $request->query('twitter_username')
            ? $request->twitter_username
            : auth()->user()->twitter_username;

        $tweets = Tweet::whereHas('user', function ($q) use ($twitterUsername) {
            $q->where('twitter_username', '=', $twitterUsername);
        })->orderBy('published_at', 'desc')->paginate(20);

        return response([
            'data' => [ 'tweets' => $tweets ],
            'message' => 'Success!'
        ], 200);
    }

    public function syncWithRemote()
    {
        auth()->user()->syncTweets();
        return response([
            'message' => 'Tweets synchronized'
        ], 200);
    }

    public function publish($id)
    {
        $tweet = Tweet::where('user_id', auth()->user()->id)->where('id', $id)->first();
        if (!$tweet) {
            return response([
                'message' => 'Tweet not found'
            ], 404);
        }
        $tweet->is_published = true;
        $tweet->update();
        return response([
            'data' => [ 'tweet' => $tweet ],
            'message' => 'Tweet Published!'
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'content'  => 'required|string|max:280'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response([
                'message' => $validator->messages()
            ], 400);
        }

        $tweet = Tweet::where('user_id', auth()->user()->id)->where('id', $id)->first();
        if (!$tweet) {
            return response([
                'message' => 'Tweet not found'
            ], 404);
        }
        $tweet->content = $request['content'];
        $tweet->update();
        return response([
            'data' => [ 'tweet' => $tweet ],
            'message' => 'Tweet Updated!'
        ], 200);
    }
}
