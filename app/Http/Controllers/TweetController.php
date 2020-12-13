<?php

namespace App\Http\Controllers;

use App\Models\Tweet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class TweetController extends Controller
{
    public function getAllTweets(Request $request) {
        if ($request->has('twitter_username')) {
            $twitter_username = $request->twitter_username;
        } else {
            // TODO: Auth olmuş kullanıcının username alacağım
            $twitter_username = 'sinan_ozata';
        }
        $tweets = Tweet::whereHas('user', function($q) use ($twitter_username) {
            $q->where('twitter_username', '=', $twitter_username);
        })->orderBy('published_at', 'desc')->paginate(20);
        return response()->json($tweets,200);
    }

    public function syncLastTweetsWithRemote() {
        $tweetList = $this->getLastTweetsFromRemote(auth()->user()->twitter_username);
        $this->saveTweets(auth()->user()->id,$tweetList);
    }

    public function publishTweet($id) {
        // TODO: tweet i paylaş
        $tweet = Tweet::findOrFail($id);
    }

    public function updateTweet(Request $request, $id) {
        $rules = array(
            'content'  => 'required|string|max:280'
        );
        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->messages(),400);
        } else {
            $tweet = Tweet::findOrFail($id);
            $tweet->content = $request['content'];
            $tweet->update();
            return response()->json('Tweet Updated',200);
        }
    }

    private function getLastTweetsFromRemote(string $username): array {
        try {
            return \Twitter::getUserTimeline(['screen_name' => $username, 'count' => 20, 'format' => 'array']);
        }
        catch (\Exception $e)
        {
            Log::error(\Twitter::logs());
        }
    }

    private function saveTweets(int $user_id, array $tweetList){
        $mappedTweetList = $this->mapToArrayTweets($user_id, $tweetList);
        DB::table('tweets')->insertOrIgnore($mappedTweetList);
    }

    private function mapToArrayTweets(int $user_id, array $tweetList): array {
        $mappedList = [];
        foreach( $tweetList as $key => $value ) {
            $mappedTweet = [
                'content' => $value['text'],
                'published_at' => $value['created_at'],
                'tweet_remote_id' => $value['id_str'],
                'user_id' => $user_id
            ];
            array_push($mappedList, $mappedTweet);
        }
        return $mappedList;
    }

}