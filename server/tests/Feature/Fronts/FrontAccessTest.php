<?php

namespace Tests\Feature\Fronts;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

/**
 * MEMO: logクラスが無いというエラーが出たら「php artisan optimize」を行う事
 */
class FrontAccessTest extends TestCase
{
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
        $user = $this->makeTestUser();
        $this->withoutExceptionHandling();
        // ログイン状態でhome画面にアクセスする
        $response = $this->actingAs($user)->get(route('home.index'));
        $response->assertStatus(200)
            ->assertViewIs('home.index');
    }

    // /**
    //  * @test
    //  */
    public function newsにアクセスできるか()
    {
        // $user = User::first();
        $user = $this->makeTestUser();
        // $this->withoutExceptionHandling();
        // ログイン状態でnews画面にアクセスする
        $response = $this->actingAs($user)->get(route('news.top'));

        $response->assertStatus(200)
            ->assertViewIs('news.top')
            ->assertSee('News');
    }

    // /**
    //  * @test
    //  */
    public function rankingにアクセスできるか()
    {
        $user = $this->makeTestUser();
        $this->withoutExceptionHandling();
        // ログイン状態でnews画面にアクセスする
        $response = $this->actingAs($user)->get(route('ranking.top'));

        $response->assertStatus(200)
            ->assertViewIs('ranking.top')
            ->assertSee('Ranking');
    }

    // /**
    //  * @test
    //  */
    public function favorite_brandにアクセスできるか()
    {
        $user = $this->makeTestUser();
        $this->withoutExceptionHandling();
        // ログイン状態でnews画面にアクセスする
        $response = $this->actingAs($user)->get(route('favorite_brand.top'));

        $response->assertStatus(200)
            ->assertViewIs('favorite_brand.top')
            ->assertSee('Brand');
    }

    // /**
    //  * @test
    //  */
    public function favorite_playerにアクセスできるか()
    {
        $user = User::first();
        $this->withoutExceptionHandling();
        // ログイン状態でnews画面にアクセスする
        $response = $this->actingAs($user)->get(route('favorite_player.top'));

        $response->assertStatus(200)
            ->assertViewIs('favorite_player.top')
            ->assertSee('Player');
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