<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScrapeTourScheduleCommand extends Command
{
    protected $signature = 'command:scrapeTourSchedule';
    protected $description = 'Command description';

    const URL = 'https://www.atptour.com/en/tournaments';
    const LOOP_COUNT = 14121;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
    }
}
