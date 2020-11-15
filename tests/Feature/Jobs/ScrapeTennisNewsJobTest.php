<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ScrapeBrandNewsJob;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Illuminate\Support\Collection;
use Mockery\MockInterface;
use Illuminate\Support\Facades\Artisan;
use App\Repositories\Contracts\NewsArticlesRepository;
use App\Jobs\ScrapeTennisNewsJob;
use Illuminate\Support\Facades\Bus;
use Carbon\Carbon;

class ScrapeTennisNewsJobTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Application
     */
    private $news_articles_repository_mock;

    public function SetUp()
    {
        parent::setUp();
        // リポジトリをモック
        $this->setMockery();
        // インスタンスを指定
        $this->setMockInstance();
    }

    public function tearDown()
    {
        parent::tearDown(); 
        Mockery::close();
    }

    private function setMockery()
    {
        $this->news_articles_repository_mock = Mockery::mock(NewsArticlesRepository::class);
    }

    private function setMockInstance()
    {
        $this->app->instance(NewsArticlesRepository::class, $this->news_articles_repository_mock);
    }

    /**
     * Handleのテスト
     * 処理の中でリポジトリメソッドの返り値を必要としない場合はメソッドをセットしなくて良いみたい。書くとエラー。
     * @test
     */
    public function Handleのテスト()
    {
        Bus::fake();

        dispatch_now( new ScrapeTennisNewsJob );

        Bus::assertDispatched( ScrapeTennisNewsJob::class, function ($job) {
            $job->handle(
                $this->news_articles_repository_mock
            );
            return true;
        });
    }
}
