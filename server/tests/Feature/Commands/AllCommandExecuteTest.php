<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use App\Jobs\ScrapeBrandNewsJob;

class AllCommandExecuteTest extends TestCase
{
    /**
     * @test
     */
    public function command_getBrandsYoutubeが実行できるか()
    {
        $this->artisan('command:getBrandsYoutube');
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function command_getPlayersYoutubeが実行できるか()
    {
        $this->artisan('command:getPlayersYoutube');
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function command_scrapeBrandNewsが実行できジョブがディスパッチされるか()
    {
        Bus::fake();

        $this->artisan('command:scrapeBrandNews', ['--sync' => true]);
        $this->assertTrue(true);
        // 検証：指定のジョブがディスパッチされたかどうか。
        Bus::assertDispatched(ScrapeBrandNewsJob::class);
    }

    /**
     * @test
     */
    public function command_scrapeNewsが実行できるか()
    {
        $this->artisan('command:scrapeNews');
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function command_scrapeRankingが実行できるか()
    {
        $this->artisan('command:scrapeRanking');
        $this->assertTrue(true);
    }


    /**
     * @test
     */
    public function command_scrapeTennisNewsが実行できるか()
    {
        $this->artisan('command:scrapeTennisNews');
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function command_scrapeTourScheduleが実行できるか()
    {
        $this->artisan('command:scrapeTourSchedule');
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function command_updatePlayerが実行できるか()
    {
        $this->artisan('command:updatePlayer');
        $this->assertTrue(true);
    }
}
