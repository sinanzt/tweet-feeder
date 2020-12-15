<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testUserCanRegister()
    {
        $fakeName = $this->faker->name;
        $fakeEmail = $this->faker->unique()->safeEmail;
        $fakePhone = $this->faker->phoneNumber;
        $fakeTwitterUsername = 'sinan_ozata';
        $fakePassword = 'Password1234@';

        $userData = [
            'name' => $fakeName,
            'email' => $fakeEmail,
            'phone' => $fakePhone,
            'twitter_username' => $fakeTwitterUsername,
            'password' => $fakePassword,
            'password_confirm' => $fakePassword
        ];

        $this->postJson(route('register'), $userData)
            ->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'name' => $fakeName,
            'email' => $fakeEmail,
            'phone' => $fakePhone,
            'twitter_username' => $fakeTwitterUsername,
        ]);
    }

    public function testUserRegisterValidate()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'twitter_username' => $this->faker->userName,
            'password' => 'Password1234',
            'password_confirm' => 'Password1234'
        ];

        $this->postJson(route('register'), $userData)
            ->assertStatus(400);
    }

    public function testUserAuthenticate()
    {
        $fakeName = $this->faker->name;
        $fakeEmail = $this->faker->unique()->safeEmail;
        $fakePhone = $this->faker->phoneNumber;
        $fakeTwitterUsername = 'sinan_ozata';
        $fakePassword = 'Password1234@';

        $userData = [
            'name' => $fakeName,
            'email' => $fakeEmail,
            'phone' => $fakePhone,
            'twitter_username' => $fakeTwitterUsername,
            'password' => $fakePassword,
            'password_confirm' => $fakePassword
        ];

        $loginData = [
            'email' => $fakeEmail,
            'password' => $fakePassword
        ];

        $this->postJson(route('register'), $userData)
            ->assertStatus(200);

        $this->postJson(route('login'), $loginData)
            ->assertStatus(200);
    }

    public function testUserAuthenticateValidate()
    {
        $fakeEmail = $this->faker->unique()->safeEmail;
        $fakePassword = 'Password';

        $loginData = [
            'email' => $fakeEmail,
            'password' => $fakePassword
        ];

        $this->postJson(route('login'), $loginData)
            ->assertStatus(401);
    }

    public function testValidateUserEMail()
    {
        $validateEmailData = [
            'email' => $this->faker->unique()->safeEmail,
            'code' => Str::random(6)
        ];

        $this->postJson(route('validate.email'), $validateEmailData)
            ->assertStatus(404);
    }
}
