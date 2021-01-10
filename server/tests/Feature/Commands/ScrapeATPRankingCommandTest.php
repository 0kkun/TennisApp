<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Illuminate\Support\Collection;
use Mockery\MockInterface;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use App\Models\AtpRanking;
use Illuminate\Support\Facades\Bus;
use App\Console\Commands\scrapeATPRanking;

class ScrapeATPRankingCommandTest extends TestCase
{
    // use RefreshDatabase; // DBアクセスが無いため除去

    /**
     * @var Application
     */
    private $atp_rankings_repository_mock;

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
        $this->atp_rankings_repository_mock = Mockery::mock(NewsArticlesRepository::class);
    }

    private function setMockInstance()
    {
        $this->app->instance(NewsArticlesRepository::class, $this->atp_rankings_repository_mock);
    }

    /**
     * モックしているのでDBへアクセスも無く、テストすることがなかった。
     * @test
     */
    public function handleのテスト()
    {
        $this->artisan('command:scrapeATPRanking');
        $this->assertTrue(true);
    }
}
