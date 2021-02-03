<?php

namespace Tests\Feature\Apis;

use Tests\TestCase;
use App\Models\User;
use App\Services\Api\ApiServiceInterface;
use Illuminate\Support\Collection;
use App\Services\Api\ApiService;
use App\Models\TourSchedule;
use App\Repositories\Contracts\TourScheduleRepository;
use App\Repositories\Eloquents\EloquentTourScheduleRepository;

class TourScheduleApiTest extends TestCase
{
    const API_BASE_URL = '/api/v1/';

    private $tour_schedule_repository_mock;
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
        $this->tour_schedule_repository_mock = \Mockery::mock(TourScheduleRepository::class);
        $this->api_service_mock = \Mockery::mock(ApiServiceInterface::class);
    }

    private function setMockInstance()
    {
        $this->app->instance(EloquentTourScheduleRepository::class, $this->tour_schedule_repository_mock);
        $this->app->instance(ApiService::class, $this->api_service_mock);
    }


    /**
     * @test
     */
    public function Api_fetchTourSchedules_正しいリクエストが来たら正しくレスポンスを返すか()
    {
        // データをセット
        $num = 10;
        $tour_schedules = factory(TourSchedule::class, $num)->make();

        $api_request = ['num' => $num];

        $api_test_url = self::API_BASE_URL . 'tour_schedules';
        $this->setTourScheduleRepositoryMethod('getAll', $tour_schedules);
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
    public function Api_fetchTourSchedules_不正なリクエストならバリデーションエラーになるか()
    {
        $api_request = ['name' => 'taro']; // わざと間違ったリクエストパラメータ
        $api_test_url = self::API_BASE_URL . 'tour_schedules';

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
    public function Api_fetchTourSchedules_ログインしていない状態で実行すると認証エラーになるか()
    {
        $api_request = ['num' => 10];
        $api_test_url = self::API_BASE_URL . 'tour_schedules';

        // GETリクエスト。未ログイン状態で行う
        $json_response = $this->json('GET', $api_test_url, $api_request);

        // 認証エラーのステータスコードになっているか
        $json_response->assertStatus(401);
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
     * TourScheduleRepositoryのメソッドをセット
     *
     * @param string $method
     * @param Collection $return
     * @return void
     */
    private function setTourScheduleRepositoryMethod(string $method, Collection $return): void
    {
        $this->tour_schedule_repository_mock
            ->shouldReceive($method)
            ->andReturn($return);
    }
}
