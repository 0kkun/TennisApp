<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ScrapeBrandNewsJob;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use App\Repositories\Contracts\NewsArticlesRepository;
use App\Jobs\ScrapeTennisNewsJob;
use Illuminate\Support\Facades\Bus;

class ScrapeTennisNewsJobTest extends TestCase
{
    // use RefreshDatabase; // DBアクセスが無いため除去

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
        Mockery::close();
        parent::tearDown(); 
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
    public function Job_scrapeTennisNewsが正常にディスパッチできるか()
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
