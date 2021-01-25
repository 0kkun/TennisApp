<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ScrapeBrandNewsJob;

class ScrapeBrandNewsCommand extends Command
{
    protected $signature = 'command:scrapeBrandNews {--sync : 同期処理}';
    protected $description = 'ブランドのニュースをスクレイピングで取得し保存するコマンド';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $is_sync = $this->option('sync');

        // 同期実行か非同期実行か判定
        if ($is_sync) {
            $this->info('同期実行開始');
            dispatch_now(new ScrapeBrandNewsJob);
        } else {
            $this->info('非同期実行開始');
            dispatch(new ScrapeBrandNewsJob);
        }

        $this->info('実行完了');
    }
}
