<?php

namespace App\Http\Controllers;

use App\Models\Tweet;
use Illuminate\Http\Request;

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
 *  @OA\Get(
 *      path="/api/tweets",
 *      tags={"Tweet Endpoints"},
 *      security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *          name="twitter_username",
 *          in="query",
 *          required=false,
 *          description="Twitter username",
 *          @OA\Schema(
 *              type="string"
 *          ),
 *      ),
 *      @OA\Response(response="200", description="Listing of Tweets paginate with size 20.")
 *  )
 */

/**
 *  @OA\Put(
 *      path="/api/tweets/{id}",
 *      tags={"Tweet Endpoints"},
 *      security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          @OA\Schema(
 *              type="integer"
 *          )
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
 *      tags={"Tweet Endpoints"},
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
 *      @OA\Response(response="200", description="Tweet is published to remote")
 *  )
 */

/**
 *  @OA\Post(
 *      path="/api/tweets/sync",
 *      tags={"Tweet Endpoints"},
 *      security={{"bearerAuth":{}}},
 *      @OA\Response(response="200", description="Tweets syncronized with remote")
 *  )
 */

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
        $request->validate([
            'content'  => 'required|string|max:280'
        ]);

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
