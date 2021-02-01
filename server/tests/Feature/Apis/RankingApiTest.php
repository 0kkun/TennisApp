<?php

namespace Tests\Feature\Apis;

use App\Models\Ranking;
use App\Models\User;
use Tests\TestCase;
use App\Repositories\Contracts\RankingRepository;
use App\Services\Api\ApiServiceInterface;
use Illuminate\Support\Collection;
use App\Services\Api\ApiService;
use App\Repositories\Eloquents\EloquentRankingRepository;


class RankingApiTest extends TestCase
{
    const API_BASE_URL = '/api/v1/';

    private $ranking_repository_mock;
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
        $this->ranking_repository_mock = \Mockery::mock(RankingRepository::class);
        $this->api_service_mock = \Mockery::mock(ApiServiceInterface::class);
    }

    private function setMockInstance()
    {
        $this->app->instance(EloquentRankingRepository::class, $this->ranking_repository_mock);
        $this->app->instance(ApiService::class, $this->api_service_mock);
    }


    /**
     * @test
     */
    public function Api_fetchRankings_正しいリクエストが来たら正しくレスポンスを返すか()
    {
        // データをセット
        $num = 10;
        $rankings = factory(Ranking::class, $num)->make();

        $api_request = ['num' => $num];

        $api_test_url = self::API_BASE_URL . 'rankings';
        $this->setRankingRepositoryMethod('fetchRankings', $rankings);
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
        $this->assertEquals($num, count($decode_response->data));
    }


    /**
     * @test
     */
    public function Api_fetchRankings_不正なリクエストならバリデーションエラーになるか()
    {
        $api_request = ['name' => 'taro']; // わざと間違ったリクエストパラメータ
        $api_test_url = self::API_BASE_URL . 'rankings';

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
    public function Api_fetchRankings_ログインしていない状態で実行すると認証エラーになるか()
    {
        $api_request = ['num' => 10];
        $api_test_url = self::API_BASE_URL . 'rankings';

        // GETリクエスト。未ログイン状態で行う
        $json_response = $this->json('GET', $api_test_url, $api_request);

        // 認証エラーのステータスコードになっているか
        $json_response->assertStatus(401);
    }


    /**
     * RankingRepositoryのメソッドをセット
     *
     * @param string $method
     * @param Collection $return
     * @return void
     */
    private function setRankingRepositoryMethod(string $method, Collection $return): void
    {
        $this->ranking_repository_mock
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
