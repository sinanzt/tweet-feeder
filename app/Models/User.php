<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PHPUnit\Util\Exception;
use Thujohn\Twitter\Facades\Twitter;

class User extends Authenticatable
{

    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'twitter_username'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    /**
     * @var mixed
     */


    public function rollApiToken()
    {
        do {
            $this->token = Str::random(60);
        } while ($this->where('token', $this->token)->exists());
        $this->save();
    }

    public function tweets()
    {
        return $this->hasMany(Tweet::class);
    }

    public function sendCodeForValidate()
    {
        $email_code = $this->generateCodeForEmail();
        $phone_code = $this->generateCodeForPhone();
        $this->saveValidateCode($email_code, $phone_code);

        Log::info("For ". $this->email. " email verification code: " . $email_code);
        Log::info("For ". $this->phone. " phone verification code: " . $phone_code);
    }

    public function syncTweets()
    {
        $tweetList = [];
        try {
            $tweetList = $this->getLastTweetsFromRemote($this->twitter_username);
        } catch (Exception $e) {
            /* Bu kısımda gelen Twitter loglarına göre response dönülebilir */
            Log::error(Twitter::logs());
        }
        if (count($tweetList) > 0) {
            $this->saveTweets($this->id, $tweetList);
        }
    }

    private function getLastTweetsFromRemote(string $username): array
    {
        return Twitter::getUserTimeline(['screen_name' => $username, 'count' => 20, 'format' => 'array']);
    }

    private function saveTweets(int $user_id, array $tweetList)
    {
        $mappedTweetList = $this->mapToTweetList($user_id, $tweetList);
        DB::table('tweets')->insertOrIgnore($mappedTweetList);
    }

    private function mapToTweetList(int $user_id, array $tweetList): array
    {
        $mappedList = [];
        foreach ($tweetList as $key => $value) {
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

    private function saveValidateCode(string $email_code, int $phone_code)
    {
        $this->email_validate_code = $email_code;
        $this->phone_validate_code = $phone_code;
        $this->update();
    }

    private function generateCodeForPhone():int
    {
        return rand(1000, 9999);
    }

    private function generateCodeForEmail():string
    {
        return  Str::random(6);
    }

}
