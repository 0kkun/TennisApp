<?php

namespace Tests\Feature\Apis;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\FavoritePlayer;
use App\Models\Player;
use App\Models\User;
use Illuminate\Support\Collection;
use App\Repositories\Eloquents\EloquentFavoritePlayersRepository;
use App\Repositories\Contracts\FavoritePlayersRepository;
use App\Repositories\Eloquents\EloquentPlayersRepository;
use App\Repositories\Contracts\PlayersRepository;
use App\Services\Api\ApiServiceInterface;
use App\Services\Api\ApiService;

class FavoritePlayerApiTest extends TestCase
{
    private $favorite_player_repository_mock;
    private $player_repository_mock;
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
        $this->player_repository_mock = \Mockery::mock(PlayersRepository::class);
        $this->api_service_mock = \Mockery::mock(ApiServiceInterface::class);
    }

    private function setMockInstance()
    {
        $this->app->instance(EloquentFavoritePlayersRepository::class, $this->favorite_player_repository_mock);
        $this->app->instance(EloquentPlayersRepository::class, $this->player_repository_mock);
        $this->app->instance(ApiService::class, $this->api_service_mock);
    }


    /**
     * @test
     */
    public function Api_fetchPlayers_正しいリクエストを送れば正しくレスポンスを返すか()
    {
        // データをセット
        $player_num = 10;
        $test_data = $this->makeTestDataForFavoritePlayer($player_num);

        $api_request = ['user_id' => $this->login_user->id];

        // モックにメソッドをセット
        $this->setPlayerRepositoryMethod('getAll', $test_data['players']);
        $this->setFavoritePlayerRepositoryMethod('getAll', $test_data['favorite_players']);
        $this->setApiServiceMethod('calcTime', 0.5);

        // GETリクエスト。ログイン状態で行う
        $json_response = $this->actingAs($this->login_user, 'web')->json('GET', route('players.fetch'), $api_request);

        // jsonの中身をチェックする為デコード
        $decode_response = json_decode($json_response->content());

        // デフォルトレスポンスの検証
        $json_response->assertOk();

        // オリジナル設定したステータスの確認
        $this->assertEquals(200, $decode_response->status);

        // レスポンスにデータが含まれているか
        $this->assertNotEmpty($decode_response->data);
    }


    /**
     * @test
     */
    public function Api_fetchPlayers_不正なリクエストならバリデーションエラーになるか()
    {
        $api_request = ['user_id' => 'taro']; // わざと間違ったリクエストパラメータ

        $this->setApiServiceMethod('calcTime', 0.5);

        // GETリクエスト。ログイン状態で行う
        $json_response = $this->actingAs($this->login_user, 'web')->json('GET', route('players.fetch'), $api_request);

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
    public function Api_fetchPlayers_ログインしていない状態で実行すると認証エラーになるか()
    {
        $api_request = ['num' => 10];

        // GETリクエスト。未ログイン状態で行う
        $json_response = $this->json('GET', route('players.fetch'), $api_request);

        // 認証エラーのステータスコードになっているか
        $json_response->assertStatus(401);
    }


    /**
     * @test
     */
    public function Api_addPlayer_正しいリクエストを送れば正しくレスポンスを返すか()
    {
        $api_request = [
            'user_id'            => $this->login_user->id,
            'favorite_player_id' => 1
        ];

        // モックにメソッドをセット
        $this->setFavoritePlayerRepositoryMethod('bulkInsertOrUpdate');
        $this->setApiServiceMethod('calcTime', 0.5);

        // GETリクエスト。ログイン状態で行う
        $json_response = $this->actingAs($this->login_user, 'web')->json('POST', route('players.add'), $api_request);

        // jsonの中身をチェックする為デコード
        $decode_response = json_decode($json_response->content());

        // デフォルトレスポンスの検証
        $json_response->assertOk();

        // オリジナル設定したステータスの確認
        $this->assertEquals(201, $decode_response->status);
    }


    /**
     * @test
     */
    public function Api_addPlayer_不正なリクエストならバリデーションエラーになるか()
    {
        // わざと間違ったリクエストパラメータ
        $api_request = [
            'user_id'            => $this->login_user->id,
            'favorite_player_id' => 'taro'
        ];

        $this->setApiServiceMethod('calcTime', 0.5);

        // GETリクエスト。ログイン状態で行う
        $json_response = $this->actingAs($this->login_user, 'web')->json('POST', route('players.add'), $api_request);

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
    public function Api_addPlayer_ログインしていない状態で実行すると認証エラーになるか()
    {
        $api_request = [
            'user_id'            => $this->login_user->id,
            'favorite_player_id' => 1
        ];

        // GETリクエスト。未ログイン状態で行う
        $json_response = $this->json('POST', route('players.add'), $api_request);

        // 認証エラーのステータスコードになっているか
        $json_response->assertStatus(401);
    }


    /**
     * @test
     */
    public function Api_deletePlayer_正しいリクエストを送れば正しくレスポンスを返すか()
    {
        $api_request = [
            'user_id'            => $this->login_user->id,
            'favorite_player_id' => 1
        ];

        // モックにメソッドをセット
        $this->setFavoritePlayerRepositoryMethod('deleteRecord');
        $this->setApiServiceMethod('calcTime', 0.5);

        // GETリクエスト。ログイン状態で行う
        $json_response = $this->actingAs($this->login_user, 'web')->json('DELETE', route('players.delete'), $api_request);

        // jsonの中身をチェックする為デコード
        $decode_response = json_decode($json_response->content());

        // デフォルトレスポンスの検証
        $json_response->assertOk();

        // オリジナル設定したステータスの確認
        $this->assertEquals(202, $decode_response->status);
    }


    /**
     * @test
     */
    public function Api_deletePlayer_不正なリクエストならバリデーションエラーになるか()
    {
        // わざと間違ったリクエストパラメータ
        $api_request = [
            'user_id'            => $this->login_user->id,
            'favorite_player_id' => 'taro'
        ];

        $this->setApiServiceMethod('calcTime', 0.5);

        // GETリクエスト。ログイン状態で行う
        $json_response = $this->actingAs($this->login_user, 'web')->json('DELETE', route('players.delete'), $api_request);

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
    public function Api_deletePlayer_ログインしていない状態で実行すると認証エラーになるか()
    {
        $api_request = [
            'user_id'            => $this->login_user->id,
            'favorite_player_id' => 1
        ];

        // GETリクエスト。未ログイン状態で行う
        $json_response = $this->json('DELETE', route('players.delete'), $api_request);

        // 認証エラーのステータスコードになっているか
        $json_response->assertStatus(401);
    }


    /**
     * player player apiテスト用のテストデータ作成
     *
     * @param integer $player_num
     * @param integer $expected_news_num
     * @return array
     */
    private function makeTestDataForFavoritePlayer(int $player_num): array
    {
        $players = factory(Player::class, $player_num)->make();

        $favorite_players = collect();

        foreach ($players as $player) {
            $favorite_players = $favorite_players->concat(
                factory(FavoritePlayer::class, 1)->make([
                    'user_id'   => $this->login_user->id,
                    'player_id' => $player->id,
                    'name_jp'   => $player->name_jp,
                    'country'   => $player->country
                ])
            );
        }

        return [
            'players'          => $players,
            'favorite_players' => $favorite_players
        ];
    }


    /**
     * FavoritePlayerRepositoryのメソッドをセット
     *
     * @param string $method
     * @param Collection $return
     * @return void
     */
    private function setFavoritePlayerRepositoryMethod(string $method, ?Collection $return=null)
    {
        $this->favorite_player_repository_mock
            ->shouldReceive($method)
            ->andReturn($return);
    }


    /**
     * PlayerRepositoryのメソッドをセット
     *
     * @param string $method
     * @param Collection $return
     * @return void
     */
    private function setPlayerRepositoryMethod(string $method, Collection $return)
    {
        $this->player_repository_mock
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
}
