<?php

namespace Tests\Feature\Fronts;

use Tests\TestCase;
use App\Models\User;

class FrontAccessTest extends TestCase
{
    private $user;

    protected function setUp()
    {
        parent::setUp();
        $this->user = $this->makeTestUser();
    }


    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function loginにアクセスできるか()
    {
        // 例外処理を抑止
        // phpunitの出力にエラーがそのまま出てくるようになる
        $this->withoutExceptionHandling();
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function Topにアクセスできるか()
    {
        $this->withoutExceptionHandling();
        $response = $this->get(route('top.index'));
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function homeにアクセスできるか()
    {
        $this->withoutExceptionHandling();
        // ログイン状態でhome画面にアクセスする
        $response = $this->actingAs($this->user)->get(route('home.index'));
        $response->assertStatus(200)
            ->assertViewIs('home.index');
    }


    /**
     * Userを作成する
     * ログイン後の画面を確認する為
     *
     * @return User
     */
    private function makeTestUser(): User
    {
        return factory(User::class)->make();
    }
}