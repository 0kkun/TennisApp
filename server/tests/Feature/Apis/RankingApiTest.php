<?php

namespace Tests\Feature\Apis;

use App\Models\Ranking;
use Tests\TestCase;
use App\Repositories\Contracts\RankingRepository;
use App\Services\Api\ApiServiceInterface;
use Illuminate\Support\Collection;
use App\Services\Api\ApiService;
use App\Repositories\Eloquents\EloquentRankingRepository;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class RankingApiTest extends TestCase
{
    // ミドルウェアの無効化.「Unauthenticated」(未認証) が出る為.
    use WithoutMiddleware;

    const API_BASE_URL = '/api/v1/';

    private $ranking_repository_mock;
    private $api_service_mock;

    public function setUp()
    {
        parent::setUp();
        // リポジトリをモック
        $this->setMockery();
        // インスタンスを指定
        $this->setMockInstance();
    }

    public function tearDown()
    {
        \Mockery::close();
        parent::tearDown(); 
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
        $this->setApiServiceMethod('checkArgs', 200);

        // GET リクエスト
        $json_response = $this->json('GET', $api_test_url, $api_request);

        // jsonの中身をチェックする為デコード
        $decode_response = json_decode($json_response->content());

        // レスポンスの検証
        $json_response->assertOk();

        // オリジナル設定したステータスの確認
        $this->assertEquals(200, $decode_response->status);

        // データの取得件数は合っているか
        $this->assertEquals($num, count($decode_response->data));
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
