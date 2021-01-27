<?php

namespace App\Console\Commands\old;

use Illuminate\Console\Command;
use Weidner\Goutte\GoutteFacade;
use App\Modules\BatchLogger;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Jobs\ScrapeTennisNewsJob;


class scrapeTennisNews extends Command
{
    protected $signature = 'command:scrapeTennisNews {--sync : 同期処理}';
    protected $description = 'テニスのニュースをスクレイピングで取得し保存するコマンド';


    public function __construct(
    )
    {
        parent::__construct();
    }


    /**
     *
     * @return mixed
     */
    public function handle()
    {
        $is_sync = $this->option('sync');

        // 同期実行か非同期実行か判定
        if ($is_sync) {
            $this->info('同期実行開始');
            dispatch_now(new ScrapeTennisNewsJob);
        } else {
            $this->info('非同期実行開始');
            dispatch(new ScrapeTennisNewsJob);
        }

        $this->info('実行完了');
    }
}
