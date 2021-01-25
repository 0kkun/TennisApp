<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

class UserRegisterTest extends TestCase
{
    /**
     * @test
     */
    public function ユーザー登録できるか()
    {
        $user = factory(User::class)->make();
        $user_inputs = [
            'name'                  => $user->name,
            'email'                 => $user->email,
            'password'              => $user->password,
            'password_confirmation' => $user->password,
        ];

        $this->post(route('register'), $user_inputs)
            ->assertStatus(302);
    }
}
