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
     * @test
     */
    public function newsにアクセスできるか()
    {
        $this->withoutExceptionHandling();
        // ログイン状態でnews画面にアクセスする
        $response = $this->actingAs($this->user)->get(route('news.top'));

        $response->assertStatus(200)
            ->assertViewIs('news.top')
            ->assertSee('News');
    }

    /**
     * @test
     */
    public function rankingにアクセスできるか()
    {
        $this->withoutExceptionHandling();
        // ログイン状態でnews画面にアクセスする
        $response = $this->actingAs($this->user)->get(route('ranking.top'));

        $response->assertStatus(200)
            ->assertViewIs('ranking.top')
            ->assertSee('Ranking');
    }

    /**
     * @test
     */
    public function favorite_brandにアクセスできるか()
    {
        $this->withoutExceptionHandling();
        // ログイン状態でnews画面にアクセスする
        $response = $this->actingAs($this->user)->get(route('favorite_brand.top'));

        $response->assertStatus(200)
            ->assertViewIs('favorite_brand.top')
            ->assertSee('Brand');
    }

    /**
     * @test
     */
    public function favorite_playerにアクセスできるか()
    {
        $this->withoutExceptionHandling();
        // ログイン状態でnews画面にアクセスする
        $response = $this->actingAs($this->user)->get(route('favorite_player.top'));

        $response->assertStatus(200)
            ->assertViewIs('favorite_player.top')
            ->assertSee('Player');
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