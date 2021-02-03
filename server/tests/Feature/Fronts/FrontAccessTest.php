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
    public function Front_loginにアクセスできるか()
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
    public function Front_topにアクセスできるか()
    {
        $this->withoutExceptionHandling();
        $response = $this->get(route('top.index'));
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function Front_homeにアクセスできるか()
    {
        $this->withoutExceptionHandling();
        // ログイン状態でhome画面にアクセスする
        $response = $this->actingAs($this->user)->get(route('home.index'));
        $response->assertStatus(200)
            ->assertViewIs('home.index');
    }

    /**
     * @test
     */
    public function Front_newsにアクセスできるか()
    {
        $this->withoutExceptionHandling();
        $response = $this->actingAs($this->user)->get(route('news.top'));
        $response->assertStatus(200)
            ->assertViewIs('news.top');
    }

    /**
     * @test
     */
    public function Front_rankingにアクセスできるか()
    {
        $this->withoutExceptionHandling();
        $response = $this->actingAs($this->user)->get(route('ranking.top'));
        $response->assertStatus(200)
            ->assertViewIs('ranking.top');
    }

    /**
     * @test
     */
    public function Front_favorite_brandにアクセスできるか()
    {
        $this->withoutExceptionHandling();
        $response = $this->actingAs($this->user)->get(route('favorite_brand.top'));
        $response->assertStatus(200)
            ->assertViewIs('favorite_brand.top');
    }

    /**
     * @test
     */
    public function Front_favorite_playerにアクセスできるか()
    {
        $this->withoutExceptionHandling();
        $response = $this->actingAs($this->user)->get(route('favorite_player.top'));
        $response->assertStatus(200)
            ->assertViewIs('favorite_player.top');
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