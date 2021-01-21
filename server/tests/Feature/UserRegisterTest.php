<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserRegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function ユーザー登録できる()
    {
        $email = 'email@example.com';
        $this->post(route('register'), [
            'name' => 'user',
            'email' => $email,
            'password' => 'password',
            'password_confirmation' => 'password'
        ])
            ->assertStatus(302);
        $this->assertDatabaseHas('users', ['email' => $email]);
    }
}
