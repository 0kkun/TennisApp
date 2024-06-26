<?php

namespace Tests\Feature\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Illuminate\Support\Collection;
use Mockery\MockInterface;
use App\Repositories\Contracts\PlayersRepository;
use App\Services\FavoritePlayer\FavoritePlayerService;
use App\Models\Player;
use Illuminate\Support\Facades\Artisan;

class FavoritePlayerServiceTest extends TestCase
{
    private $players_repository_mock;
    private $favorite_player_service;

    protected function setUp()
    {
        parent::setUp();
        // リポジトリをモック
        $this->setMockery();
        // インスタンスを指定
        $this->setMockInstance();
        // テストするサービスを指定
        $this->favorite_player_service = app(FavoritePlayerService::class);
    }


    protected function tearDown()
    {
        Mockery::close();
        parent::tearDown(); 
    }

    private function setMockery()
    {
        $this->players_repository_mock = Mockery::mock(PlayersRepository::class);
    }

    private function setMockInstance()
    {
        $this->app->instance(PlayersRepository::class, $this->players_repository_mock);
    }


    /**
     * 正常系
     * @test
     */
    public function searchPlayersメソッドのテスト()
    {
        // データをセット
        $players = factory(Player::class, 1)->make();

        $search_player = $players->first();

        $expected_value = collect([
            'name_jp' => $search_player->name_jp,
            'country' => $search_player->country,
            'age'     => $search_player->age,
        ]);

        $input = [
            'name'    => $search_player->name_jp,
            'country' => $search_player->country,
            'age'     => $search_player->age,
        ];

        // サービスで使用するリポジトリメソッドをセット
        $this->setPlayersRepositoryMethod('searchPlayers', $input, $expected_value);

        // サービスを実行
        $result = $this->favorite_player_service->searchPlayers($input);

        // 検査
        $this->assertNotEmpty($result);
        $this->assertEquals($result['name_jp'], $expected_value['name_jp']);
        $this->assertEquals($result['country'], $expected_value['country']);
        $this->assertEquals($result['age'], $expected_value['age']);
    }


    /**
     * PlayersRepositoryのメソッドをセット
     *
     * @param string $method
     * @param array $input
     * @param Collection $return
     * @return void
     */
    private function setPlayersRepositoryMethod(string $method, array $input, Collection $return): void
    {
        $this->players_repository_mock->shouldReceive($method)
            ->with($input) // 複数の引数の場合はwithArgsを使う
            ->once()
            ->andReturn($return);
    }
}
