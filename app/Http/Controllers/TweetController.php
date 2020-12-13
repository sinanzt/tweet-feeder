<?php

namespace App\Http\Controllers;

use App\Models\Tweet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @OA\SecurityScheme(
 *     @OA\Flow(
 *         flow="clientCredentials",
 *         tokenUrl="oauth/token",
 *         scopes={}
 *     ),
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="bearer"
 * )
 */

/**
 *  @OA\Post(
 *      path="/api/tweet-list",
 *      tags={"tweet-endpoints"},
 *      security={{"bearerAuth":{}}},
 *      @OA\RequestBody(
 *          required=false,
 *          description="listing of Tweets",
 *          @OA\JsonContent(
 *              required=false,
 *              required={"twitter_username"},
 *              @OA\Property(property="twitter_username", type="string", example="username1"),
 *          ),
 *      ),
 *      @OA\Response(response="200", description="Display a listing of Tweets paginate with size 20.")
 *  )
 */

/**
 *  @OA\Put(
 *      path="/api/tweets/{id}",
 *      tags={"tweet-endpoints"},
 *      security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *      name="id",
 *      in="path",
 *      required=true,
 *      @OA\Schema(
 *           type="integer"
 *      )
 *      ),
 *      @OA\RequestBody(
 *          required=false,
 *          description="Tweet Update",
 *          @OA\JsonContent(
 *              required=true,
 *              required={"content"},
 *              @OA\Property(property="content", type="text", example="Tweet content")
 *          ),
 *      ),
 *      @OA\Response(response="200", description="Tweet Updated")
 *  )
 */

/**
 *  @OA\Post(
 *      path="/api/tweets/{id}/publish",
 *      tags={"tweet-endpoints"},
 *      security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          @OA\Schema(
 *           type="integer"
 *          )
 *      ),
 *      @OA\RequestBody(
 *          required=false,
 *          description="Publish Tweet with remote"
 *      ),
 *      @OA\Response(response="200", description="Tweet is published with remote")
 *  )
 */

/**
 *  @OA\Get(
 *      path="/api/tweets/sync",
 *      tags={"tweet-endpoints"},
 *      security={{"bearerAuth":{}}},
 *      @OA\Response(response="200", description="Tweets syncronized with remote")
 *  )
 */

class TweetController extends Controller
{
    public function getAllTweets(Request $request) {
        if ($request->has('twitter_username')) {
            $twitter_username = $request->twitter_username;
        } else {
            $twitter_username = auth()->user()->twitter_username;
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
        $tweet = Tweet::where('user_id', auth()->user()->id)->where('id', $id)->first();
        if ($tweet){
            return response()->json($tweet,200);
        }
        return response()->json("Tweet not found",404);
    }

    public function updateTweet(Request $request, $id) {
        $rules = array(
            'content'  => 'required|string|max:280'
        );
        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->messages(),400);
        } else {
            $tweet = Tweet::where('user_id', auth()->user()->id)->where('id', $id)->first();
            if (!$tweet){
                return response()->json("Tweet not found",404);
            }
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