<?php

namespace Tests\Feature\Apis;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use App\Models\FavoritePlayer;
use App\Models\Player;
use App\Models\User;
use App\Services\Api\ApiServiceInterface;
use App\Services\Api\ApiService;
use App\Models\YoutubeVideo;
use App\Models\BrandYoutubeVideo;
use App\Repositories\Contracts\YoutubeVideosRepository;
use App\Repositories\Eloquents\EloquentYoutubeVideosRepository;
use App\Repositories\Contracts\FavoritePlayersRepository;
use App\Repositories\Eloquents\EloquentFavoritePlayersRepository;
use App\Repositories\Contracts\BrandYoutubeVideosRepository;
use App\Repositories\Eloquents\EloquentBrandYoutubeVideosRepository;
use App\Repositories\Contracts\FavoriteBrandsRepository;
use App\Repositories\Eloquents\EloquentFavoriteBrandsRepository;


class MovieApiTest extends TestCase
{
    private $api_service_mock;
    private $login_user;
    private $favorite_player_repository_mock;
    private $player_youtube_repository_mock;
    private $favorite_brand_repository_mock;
    private $brand_youtube_repository_mock;

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
        $this->player_youtube_repository_mock = \Mockery::mock(YoutubeVideosRepository::class);
        $this->favorite_brand_repository_mock = \Mockery::mock(FavoriteBrandsRepository::class);
        $this->brand_youtube_repository_mock = \Mockery::mock(BrandYoutubeVideosRepository::class);
        $this->api_service_mock = \Mockery::mock(ApiServiceInterface::class);
    }

    private function setMockInstance()
    {
        $this->app->instance(EloquentFavoritePlayersRepository::class, $this->favorite_player_repository_mock);
        $this->app->instance(EloquentYoutubeVideosRepository::class, $this->player_youtube_repository_mock);
        $this->app->instance(EloquentFavoriteBrandsRepository::class, $this->favorite_brand_repository_mock);
        $this->app->instance(EloquentBrandYoutubeVideosRepository::class, $this->brand_youtube_repository_mock);
        $this->app->instance(ApiService::class, $this->api_service_mock);
    }


    /**
     * @test
     */
    public function Api_fetchPlayerMovies_正しいリクエストが来たら正しくレスポンスを返すか()
    {
        // データをセット
        $player_num = 10;
        $expected_movie_num = 1;
        $test_data = $this->makeTestDataForPlayerMovie($player_num, $expected_movie_num);
        $api_request = ['user_id' => $this->login_user->id];

        // モックにメソッドをセット
        $this->setFavoritePlayerRepositoryMethod('getFavoritePlayers', $test_data['favorite_players']);
        $this->setPlayerYoutubeVideoRepositoryMethod('getVideosByPlayerIds', $test_data['players_youtube_movies']);
        $this->setApiServiceMethod('calcTime', 0.5);

        // GETリクエスト。ログイン状態で行う
        $json_response = $this->actingAs($this->login_user, 'web')->json('GET', route('movies.player'), $api_request);

        // jsonの中身をチェックする為デコード
        $decode_response = json_decode($json_response->content());

        // デフォルトレスポンスの検証
        $json_response->assertOk();

        // オリジナル設定したステータスの確認
        $this->assertEquals(200, $decode_response->status);

        // データの取得件数は合っているか
        $this->assertEquals($expected_movie_num, count($decode_response->data));
    }


    /**
     * @test
     */
    public function Api_fetchPlayerMovies_不正なリクエストならバリデーションエラーになるか()
    {
        $api_request = ['user_id' => 'taro']; // わざと間違ったリクエストパラメータ

        $this->setApiServiceMethod('calcTime', 0.5);

        // GETリクエスト。ログイン状態で行う
        $json_response = $this->actingAs($this->login_user, 'web')->json('GET', route('movies.player'), $api_request);

        // jsonの中身をチェックする為デコード
        $decode_response = json_decode($json_response->content());

        // デフォルトレスポンスの検証
        $json_response->assertOk();

        // オリジナル設定したステータスの確認
        $this->assertEquals(400, $decode_response->status);

        // レスポンスデータが空であるか確認
        $this->assertEmpty($decode_response->data);

        // バリデーションエラーのステータスコードになっているか
        $this->assertEquals(400, $decode_response->status);
    }


    /**
     * @test
     */
    public function Api_fetchPlayerMovies_ログインしていない状態で実行すると認証エラーになるか()
    {
        $api_request = ['user_id' => $this->login_user->id];

        // GETリクエスト。未ログイン状態で行う
        $json_response = $this->json('GET', route('movies.player'), $api_request);

        // 認証エラーのステータスコードになっているか
        $json_response->assertStatus(401);
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
     * PlayerYoutubeRepositoryのメソッドをセット
     *
     * @param string $method
     * @param Collection $return
     * @return void
     */
    private function setPlayerYoutubeVideoRepositoryMethod(string $method, Collection $return)
    {
        $this->player_youtube_repository_mock
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

    /**
     * player movie apiテスト用のテストデータ作成
     *
     * @param integer $player_num
     * @param integer $expected_movie_num
     * @return array
     */
    private function makeTestDataForPlayerMovie(int $player_num, int $expected_movie_num): array
    {
        $players = factory(Player::class, $player_num)->make();

        $favorite_players = collect();

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

        $players_youtube_movies = factory(YoutubeVideo::class, $expected_movie_num)->make([
            'player_id' => $players->first()->id
        ]);

        return [
            'favorite_players' => $favorite_players,
            'players_youtube_movies' => $players_youtube_movies
        ];
    }
}
