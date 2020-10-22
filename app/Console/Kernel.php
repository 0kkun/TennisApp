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
        $schedule->command('scrapeTennisNews')
                ->dailyAt('23:30');

        $schedule->command('scrapeBrandNews')
                ->dailyAt('23:35');

        $schedule->command('getPlayersYoutube')
                ->dailyAt('23:40');

        $schedule->command('getBrandsYoutube')
                ->dailyAt('23:45');

        $schedule->command('scrapeATPRanking')
                ->weeklyOn(1, '23:00'); // 毎週月曜日
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
