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
use App\Repositories\Contracts\FavoriteBrandsRepository;
use App\Repositories\Eloquents\EloquentFavoriteBrandsRepository;
use App\Repositories\Contracts\BrandNewsArticlesRepository;
use App\Repositories\Eloquents\EloquentBrandNewsArticlesRepository;
use App\Models\Brand;
use App\Models\FavoriteBrand;
use App\Models\BrandNewsArticle;
use App\Services\Api\ApiServiceInterface;
use App\Services\Api\ApiService;

class NewsApiTest extends TestCase
{
    const API_BASE_URL = '/api/v1/';

    private $favorite_player_repository_mock;
    private $player_news_article_repository_mock;
    private $favorite_brand_repository_mock;
    private $brand_news_article_repository_mock;
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
        $this->player_news_article_repository_mock = \Mockery::mock(PlayersNewsArticleRepository::class);
        $this->favorite_brand_repository_mock = \Mockery::mock(FavoriteBrandsRepository::class);
        $this->brand_news_article_repository_mock = \Mockery::mock(BrandNewsArticlesRepository::class);
        $this->api_service_mock = \Mockery::mock(ApiServiceInterface::class);
    }

    private function setMockInstance()
    {
        $this->app->instance(EloquentFavoritePlayersRepository::class, $this->favorite_player_repository_mock);
        $this->app->instance(EloquentPlayersNewsArticleRepository::class, $this->player_news_article_repository_mock);
        $this->app->instance(EloquentFavoriteBrandsRepository::class, $this->favorite_brand_repository_mock);
        $this->app->instance(EloquentBrandNewsArticlesRepository::class, $this->brand_news_article_repository_mock);
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
        $test_data = $this->makeTestDataForPlayerNews($player_num, $expected_news_num);

        $api_request = ['user_id' => $this->login_user->id];
        $api_test_url = self::API_BASE_URL . 'news/players';

        $this->setFavoritePlayerRepositoryMethod('getFavoritePlayers', $test_data['favorite_players']);
        $this->setPlayerNewsArticleRepositoryMethod('fetchArticlesByPlayerNames', $test_data['players_news_articles']);
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
     * @test
     */
    public function Api_fetchPlayersNews_不正なリクエストならバリデーションエラーになるか()
    {
        $api_request = ['name' => 'taro']; // わざと間違ったリクエストパラメータ
        $api_test_url = self::API_BASE_URL . 'news/players';

        $this->setApiServiceMethod('calcTime', 0.5);

        // GETリクエスト。ログイン状態で行う
        $json_response = $this->actingAs($this->login_user, 'web')->json('GET', $api_test_url, $api_request);

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
    public function Api_fetchPlayersNews_ログインしていない状態で実行すると認証エラーになるか()
    {
        $api_request = ['num' => 10];
        $api_test_url = self::API_BASE_URL . 'news/players';

        // GETリクエスト。未ログイン状態で行う
        $json_response = $this->json('GET', $api_test_url, $api_request);

        // 認証エラーのステータスコードになっているか
        $json_response->assertStatus(401);
    }


    /**
     * @test
     */
    public function Api_fetchBrandsNews_正しいリクエストが来たら正しくレスポンスを返すか()
    {
        // データをセット
        $player_num = 10;
        $expected_news_num = 1;
        $test_data = $this->makeTestDataForBrandNews($player_num, $expected_news_num);

        $api_request = ['user_id' => $this->login_user->id];
        $api_test_url = self::API_BASE_URL . 'news/brands';

        $this->setFavoriteBrandRepositoryMethod('fetchFavoriteBrands', $test_data['favorite_brands']);
        $this->setBrandrNewsArticleRepositoryMethod('fetchArticlesByBrandNames', $test_data['brand_news_articles']);
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
     * @test
     */
    public function Api_fetchBrandNews_不正なリクエストならバリデーションエラーになるか()
    {
        $api_request = ['name' => 'taro']; // わざと間違ったリクエストパラメータ
        $api_test_url = self::API_BASE_URL . 'news/brands';

        $this->setApiServiceMethod('calcTime', 0.5);

        // GETリクエスト。ログイン状態で行う
        $json_response = $this->actingAs($this->login_user, 'web')->json('GET', $api_test_url, $api_request);

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
    public function Api_fetchBrandNews_ログインしていない状態で実行すると認証エラーになるか()
    {
        $api_request = ['num' => 10];
        $api_test_url = self::API_BASE_URL . 'news/brands';

        // GETリクエスト。未ログイン状態で行う
        $json_response = $this->json('GET', $api_test_url, $api_request);

        // 認証エラーのステータスコードになっているか
        $json_response->assertStatus(401);
    }


    /**
     * PlayerNewsArticleRepositoryのメソッドをセット
     *
     * @param string $method
     * @param Collection $return
     * @return void
     */
    private function setPlayerNewsArticleRepositoryMethod(string $method, Collection $return)
    {
        $this->player_news_article_repository_mock
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


    /**
     * FavoriteBrandRepositoryのメソッドをセット
     *
     * @param string $method
     * @param Collection $return
     * @return void
     */
    private function setFavoriteBrandRepositoryMethod(string $method, Collection $return): void
    {
        $this->favorite_brand_repository_mock
            ->shouldReceive($method)
            ->andReturn($return);
    }



    /**
     * BrandNewsArticlesRepositoryメソッドのセット
     *
     * @param string $method
     * @param Collection $return
     * @return void
     */
    private function setBrandrNewsArticleRepositoryMethod(string $method, Collection $return): void
    {
        $this->brand_news_article_repository_mock
            ->shouldReceive($method)
            ->andReturn($return);
    }


    /**
     * player news apiテスト用のテストデータ作成
     *
     * @param integer $player_num
     * @param integer $expected_news_num
     * @return array
     */
    private function makeTestDataForPlayerNews(int $player_num, int $expected_news_num): array
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
        
        $players_news_articles = factory(PlayersNewsArticle::class, $expected_news_num)->make([
            'title' => $players->first()->name_jp
        ]);

        return [
            'favorite_players' => $favorite_players,
            'players_news_articles' => $players_news_articles
        ];
    }

    /**
     * brand news apiテスト用のテストデータ作成
     *
     * @param integer $brand_num
     * @param integer $expected_news_num
     * @return array
     */
    private function makeTestDataForBrandNews(int $brand_num, int $expected_news_num): array
    {
        $brands = factory(Brand::class, $brand_num)->make();

        $favorite_brands = collect();

        foreach ($brands as $brand) {
            $favorite_brands = $favorite_brands->concat(
                factory(FavoriteBrand::class, 1)->make([
                    'user_id' => $this->login_user->id,
                    'brand_id' => $brand->id,
                    'name_en'   => $brand->name_en,
                ])
            );
        }
        
        $brand_news_articles = factory(BrandNewsArticle::class, $expected_news_num)->make([
            'title' => $brands->first()->name_en
        ]);

        return [
            'favorite_brands' => $favorite_brands,
            'brand_news_articles' => $brand_news_articles
        ];
    }
}
