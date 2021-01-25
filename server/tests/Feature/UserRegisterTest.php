<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

class UserRegisterTest extends TestCase
{
    private $user;

    public function SetUp()
    {
        parent::setUp();
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('optimize:clear');
        Artisan::call('route:clear');
        $this->makeTestUser();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function ユーザー登録できるか()
    {
        $user_inputs = [
            'name'                  => $this->user->name,
            'email'                 => $this->user->email,
            'password'              => $this->user->password,
            'password_confirmation' => $this->user->password,
        ];

        $this->post(route('register'), $user_inputs)
            ->assertStatus(302);
    }

    /**
     * Userを作成する
     * ログイン後の画面を確認する為
     *
     * @return void
     */
    private function makeTestUser()
    {
        $this->user = factory(User::class)->make();
    }
}
