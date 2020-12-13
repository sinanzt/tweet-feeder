<?php

namespace Tests\Unit;


use Faker\Factory;
use Tests\TestCase;


class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */

    public function test_can_register_user() {

        $data = [
            'name' => Factory::create()->name,
            'phone' => Factory::create()->numberBetween(1000000000,9999999999),
            'twitter_username' => Factory::create()->userName,
            'email' => Factory::create()->unique()->safeEmail,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi@',
            'password_confirm' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi@'
        ];

        $this->postJson(route('register'), $data)
            ->assertStatus(200)
            ->assertJson([
                'created' => true
            ]);
    }

    public function testExample()
    {
        $this->assertTrue(true);
    }
}
