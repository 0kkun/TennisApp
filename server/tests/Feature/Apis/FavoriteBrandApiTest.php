<?php

namespace Tests\Feature\Apis;

use Tests\TestCase;
use App\Models\FavoriteBrand;
use App\Models\Brand;
use App\Models\User;
use Illuminate\Support\Collection;
use App\Repositories\Eloquents\EloquentFavoriteBrandsRepository;
use App\Repositories\Contracts\FavoriteBrandsRepository;
use App\Repositories\Eloquents\EloquentBrandsRepository;
use App\Repositories\Contracts\BrandsRepository;
use App\Services\Api\ApiServiceInterface;
use App\Services\Api\ApiService;

class FavoriteBrandApiTest extends TestCase
{
    private $favorite_brand_repository_mock;
    private $brand_repository_mock;
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
        $this->favorite_brand_repository_mock = \Mockery::mock(FavoriteBrandsRepository::class);
        $this->brand_repository_mock = \Mockery::mock(BrandsRepository::class);
        $this->api_service_mock = \Mockery::mock(ApiServiceInterface::class);
    }

    private function setMockInstance()
    {
        $this->app->instance(EloquentFavoriteBrandsRepository::class, $this->favorite_brand_repository_mock);
        $this->app->instance(EloquentBrandsRepository::class, $this->brand_repository_mock);
        $this->app->instance(ApiService::class, $this->api_service_mock);
    }

    /**
     * @test
     */
    public function Api_fetchBrands_正しいリクエストを送れば正しくレスポンスを返すか()
    {
        // データをセット
        $brand_num = 10;
        $test_data = $this->makeTestDataForFavoriteBrand($brand_num);

        $api_request = ['user_id' => $this->login_user->id];

        // モックにメソッドをセット
        $this->setBrandRepositoryMethod('getAll', $test_data['brands']);
        $this->setFavoriteBrandRepositoryMethod('getAll', $test_data['favorite_brands']);
        $this->setApiServiceMethod('calcTime', 0.5);

        // GETリクエスト。ログイン状態で行う
        $json_response = $this->actingAs($this->login_user, 'web')->json('GET', route('brands.fetch'), $api_request);

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
    public function Api_fetchBrands_不正なリクエストならバリデーションエラーになるか()
    {
        $api_request = ['user_id' => 'taro']; // わざと間違ったリクエストパラメータ

        $this->setApiServiceMethod('calcTime', 0.5);

        // GETリクエスト。ログイン状態で行う
        $json_response = $this->actingAs($this->login_user, 'web')->json('GET', route('brands.fetch'), $api_request);

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
    public function Api_fetchBrands_ログインしていない状態で実行すると認証エラーになるか()
    {
        $api_request = ['user_id' => $this->login_user->id];

        // GETリクエスト。未ログイン状態で行う
        $json_response = $this->json('GET', route('brands.fetch'), $api_request);

        // 認証エラーのステータスコードになっているか
        $json_response->assertStatus(401);
    }


    /**
     * @test
     */
    public function Api_addBrand_正しいリクエストを送れば正しくレスポンスを返すか()
    {
        $api_request = [
            'user_id'            => $this->login_user->id,
            'favorite_brand_id'  => 1
        ];

        // モックにメソッドをセット
        $this->setFavoriteBrandRepositoryMethod('bulkInsertOrUpdate');
        $this->setApiServiceMethod('calcTime', 0.5);

        // GETリクエスト。ログイン状態で行う
        $json_response = $this->actingAs($this->login_user, 'web')->json('POST', route('brands.add'), $api_request);

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
    public function Api_addBrand_不正なリクエストならバリデーションエラーになるか()
    {
        // わざと間違ったリクエストパラメータ
        $api_request = [
            'user_id'            => $this->login_user->id,
            'favorite_brand_id'  => 'taro'
        ];

        $this->setApiServiceMethod('calcTime', 0.5);

        // GETリクエスト。ログイン状態で行う
        $json_response = $this->actingAs($this->login_user, 'web')->json('POST', route('brands.add'), $api_request);

        // jsonの中身をチェックする為デコード
        $decode_response = json_decode($json_response->content());

        // デフォルトレスポンスの検証
        $json_response->assertOk();

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
            'favorite_brand_id'  => 1
        ];

        // GETリクエスト。未ログイン状態で行う
        $json_response = $this->json('POST', route('brands.add'), $api_request);

        // 認証エラーのステータスコードになっているか
        $json_response->assertStatus(401);
    }


    /**
     * @test
     */
    public function Api_deleteBrand_正しいリクエストを送れば正しくレスポンスを返すか()
    {
        $api_request = [
            'user_id'            => $this->login_user->id,
            'favorite_brand_id' => 1
        ];

        // モックにメソッドをセット
        $this->setFavoriteBrandRepositoryMethod('deleteRecord');
        $this->setApiServiceMethod('calcTime', 0.5);

        // GETリクエスト。ログイン状態で行う
        $json_response = $this->actingAs($this->login_user, 'web')->json('DELETE', route('brands.delete'), $api_request);

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
    public function Api_deleteBrand_不正なリクエストならバリデーションエラーになるか()
    {
        // わざと間違ったリクエストパラメータ
        $api_request = [
            'user_id'            => $this->login_user->id,
            'favorite_brand_id'  => 'taro'
        ];

        $this->setApiServiceMethod('calcTime', 0.5);

        // GETリクエスト。ログイン状態で行う
        $json_response = $this->actingAs($this->login_user, 'web')->json('DELETE', route('brands.delete'), $api_request);

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
    public function Api_deleteBrand_ログインしていない状態で実行すると認証エラーになるか()
    {
        $api_request = [
            'user_id'            => $this->login_user->id,
            'favorite_brand_id'  => 1
        ];

        // GETリクエスト。未ログイン状態で行う
        $json_response = $this->json('DELETE', route('brands.delete'), $api_request);

        // 認証エラーのステータスコードになっているか
        $json_response->assertStatus(401);
    }



    /**
     * favorite brand apiテスト用のテストデータ作成
     *
     * @param integer $player_num
     * @param integer $expected_news_num
     * @return array
     */
    private function makeTestDataForFavoriteBrand(int $brand_num): array
    {
        $brands = factory(Brand::class, $brand_num)->make();

        $favorite_brands = collect();

        foreach ($brands as $brand) {
            $favorite_brands = $favorite_brands->concat(
                factory(FavoriteBrand::class, 1)->make([
                    'user_id'   => $this->login_user->id,
                    'player_id' => $brand->id,
                    'name_jp'   => $brand->name_jp,
                    'country'   => $brand->country
                ])
            );
        }

        return [
            'brands'          => $brands,
            'favorite_brands' => $favorite_brands
        ];
    }

    /**
     * FavoriteBrandRepositoryのメソッドをセット
     *
     * @param string $method
     * @param Collection $return
     * @return void
     */
    private function setFavoriteBrandRepositoryMethod(string $method, ?Collection $return=null)
    {
        $this->favorite_brand_repository_mock
            ->shouldReceive($method)
            ->andReturn($return);
    }

    /**
     * BrandRepositoryのメソッドをセット
     *
     * @param string $method
     * @param Collection $return
     * @return void
     */
    private function setBrandRepositoryMethod(string $method, Collection $return)
    {
        $this->brand_repository_mock
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
