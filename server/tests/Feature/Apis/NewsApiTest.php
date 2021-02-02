<?php

namespace Tests\Feature\Apis;

use App\Models\FavoritePlayer;
use App\Models\Player;
use App\Models\PlayersNewsArticle;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Support\Collection;
use App\Repositories\Eloquents\EloquentFavoritePlayersRepository;
use App\Repositories\Contracts\FavoritePlayersRepository;
use App\Repositories\Contracts\PlayersNewsArticleRepository;
use App\Repositories\Eloquents\EloquentPlayersNewsArticleRepository;

class NewsApiTest extends TestCase
{
    const API_BASE_URL = '/api/v1/';

    private $favorite_player_repository_mock;
    private $player_news_repository_mock;
    private $api_service_mock;
    private $login_user;

    protected function setUp()
    {
        parent::setUp();
        $this->login_user = factory(User::class)->make();
        // リポジトリをモック
        $this->setMockery();
        // インスタンスを指定
        $this->setMockInstance();
    }

    protected function tearDown()
    {
        // NOTE: ReflectionException: Class config does not exist対策
        $config = app('config');
        \Mockery::close();
        parent::tearDown();
        app()->instance('config', $config);
    }

    private function setMockery()
    {
        $this->favorite_player_repository_mock = \Mockery::mock(FavoritePlayersRepository::class);
        $this->player_news_repository_mock = \Mockery::mock(PlayersNewsArticleRepository::class);
        $this->api_service_mock = \Mockery::mock(ApiServiceInterface::class);
    }

    private function setMockInstance()
    {
        $this->app->instance(EloquentFavoritePlayersRepository::class, $this->favorite_player_repository_mock);
        $this->app->instance(EloquentPlayersNewsArticleRepository::class, $this->player_news_repository_mock);
        $this->app->instance(ApiService::class, $this->api_service_mock);
    }


    /**
     * @test
     */
    public function Api_fetchPlayersNews_正しいリクエストが来たら正しくレスポンスを返すか()
    {
        // データをセット
        $player_num = 10;
        $expected_news_num = 1;
        $test_data = $this->makeTestDataForNews($player_num, $expected_news_num);

        $api_request = ['user_id' => $this->login_user->id];
        $api_test_url = self::API_BASE_URL . 'news/players';

        $this->setFavoritePlayerRepositoryMethod('getFavoritePlayers', $test_data['favorite_players']);
        $this->setPlayerNewsRepositoryMethod('fetchArticlesByPlayerNames', $test_data['players_news_articles']);
        $this->setApiServiceMethod('calcTime', 0.5);
    
        // GETリクエスト。ログイン状態で行う
        $json_response = $this->actingAs($this->login_user, 'web')->json('GET', $api_test_url, $api_request);

        // jsonの中身をチェックする為デコード
        $decode_response = json_decode($json_response->content());

        // デフォルトレスポンスの検証
        $json_response->assertOk();

        // オリジナル設定したステータスの確認
        $this->assertEquals(200, $decode_response->status);

        // データの取得件数は合っているか
        $this->assertEquals($expected_news_num, count($decode_response->data));
    }


    /**
     * PlayerNewsRepositoryのメソッドをセット
     *
     * @param string $method
     * @param Collection $return
     * @return void
     */
    private function setPlayerNewsRepositoryMethod(string $method, Collection $return)
    {
        $this->player_news_repository_mock
            ->shouldReceive($method)
            ->andReturn($return);
    }


    /**
     * FavoritePlayerRepositoryのメソッドをセット
     *
     * @param string $method
     * @param Collection $return
     * @return void
     */
    private function setFavoritePlayerRepositoryMethod(string $method, Collection $return)
    {
        $this->favorite_player_repository_mock
            ->shouldReceive($method)
            ->andReturn($return);
    }


    /**
     * ApiServiceのメソッドをセット
     *
     * @param string $method
     * @param integer $return
     * @return void
     */
    private function setApiServiceMethod(string $method, int $return): void
    {
        $this->api_service_mock
            ->shouldReceive($method)
            ->andReturn($return);
    }

    private function makeTestDataForNews(int $player_num, int $expected_news_num): array
    {
        $players = factory(Player::class, $player_num)->make();

        $favorite_players =  collect();

        foreach ($players as $player) {
            $favorite_players = $favorite_players->concat(
                factory(FavoritePlayer::class, 1)->make([
                    'user_id' => $this->login_user->id,
                    'player_id' => $player->id,
                    'name_jp'   => $player->name_jp,
                    'country'   => $player->country
                ])
            );
        }
        
        $players_news_articles = factory(PlayersNewsArticle::class, $expected_news_num)->make([
            'title' => $players->first()->name_jp
        ]);

        return [
            'favorite_players' => $favorite_players,
            'players_news_articles' => $players_news_articles
        ];
    }
}
