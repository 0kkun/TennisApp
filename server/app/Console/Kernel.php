<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
      //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // 大会スケジュールのスクレイピング
        $schedule->command('ScrapeTourScheduleCommand')
            ->dailyAt('12:00');

        // プレイヤーニュースのスクレイピング
        $schedule->command('scrapeNewsCommand')
            ->dailyAt('23:30');

        // ブランドニュースのスクレイピング
        $schedule->command('scrapeBrandNews')
            ->dailyAt('23:30');

        // Youtube APIで動画取得
        $schedule->command('getPlayersYoutube')
            ->dailyAt('22:00');
        $schedule->command('getBrandsYoutube')
            ->dailyAt('22:10');

        // ランキングのスクレイピング
        $schedule->command('scrapeRankingCommand')
            ->weeklyOn(1, '23:00'); // 毎週月曜日

        // プレイヤー情報のアップデート
        $schedule->command('UpdatePlayersCommand')
            ->weeklyOn(1, '23:10'); // 毎週月曜日
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
