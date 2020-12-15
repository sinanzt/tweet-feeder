<?php

namespace Tests\Unit;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class TweetTest extends TestCase
{
    use WithFaker;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testTweetCanUpdate()
    {
        $userData = [
            'name' => $this->faker->name,
            'phone' => $this->faker->phoneNumber,
            'twitter_username' => $this->faker->unique()->userName,
            'email' => $this->faker->unique()->email,
            'password' => Hash::make($this->faker->password),
            'token' => Str::random(60)
        ];

        $existUserData = User::factory()->create($userData);

        $tweetData = [
            'content' => $this->faker->text,
            'published_at' => $this->faker->date(),
            'tweet_remote_id' => $this->faker->uuid,
            'user_id' => $existUserData->id
        ];

        $tweetData = Tweet::factory()->create($tweetData);

        auth()->login($existUserData);
        $this->withHeader('Authorization', 'Bearer ' . $existUserData->token)
            ->putJson(route('tweets.update', $tweetData->id), [
            'content'  => $this->faker->text
        ])->assertStatus(200);
    }

    public function testTweetCanPublish()
    {
        $userData = [
            'name' => $this->faker->name,
            'phone' => $this->faker->phoneNumber,
            'twitter_username' => $this->faker->unique()->userName,
            'email' => $this->faker->unique()->email,
            'password' => Hash::make($this->faker->password),
            'token' => Str::random(60)
        ];

        $existUserData = User::factory()->create($userData);

        $tweetData = [
            'content' => $this->faker->text,
            'published_at' => $this->faker->date(),
            'tweet_remote_id' => $this->faker->uuid,
            'user_id' => $existUserData->id
        ];

        $existTweetData = Tweet::factory()->create($tweetData);

        auth()->login($existUserData);
        $this->withHeader('Authorization', 'Bearer ' . $existUserData->token)
            ->postJson(route('tweets.publish', $existTweetData->id))->assertStatus(200);
    }

    public function testTweetCanList()
    {
        $userData = [
            'name' => $this->faker->name,
            'phone' => $this->faker->phoneNumber,
            'twitter_username' => $this->faker->unique()->userName,
            'email' => $this->faker->unique()->email,
            'password' => Hash::make($this->faker->password),
            'token' => Str::random(60)
        ];

        $existUserData = User::factory()->create($userData);

        $tweetData = [
            'content' => $this->faker->text,
            'published_at' => $this->faker->date(),
            'tweet_remote_id' => $this->faker->uuid,
            'user_id' => $existUserData->id
        ];

        Tweet::factory()->create($tweetData);

        auth()->login($existUserData);
        $this->withHeader('Authorization', 'Bearer ' . $existUserData->token)
            ->getJson(route('tweets.list'))->assertStatus(200);
    }
}
