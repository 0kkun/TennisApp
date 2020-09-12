<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Weidner\Goutte\GoutteFacade;
use App\Modules\BatchLogger;
use Exception;

class scrapePlayers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:scrapePlayers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[Player] Player nameをwikiからスクレイピングで取得するコマンド';

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
     * wikiのテニス選手一覧から情報をスクレイピングする
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("実行開始");
        $this->logger = new BatchLogger('scrapePlayers');

        try {
            $this->logger->write('取得開始', 'info', true);

            $goutte = GoutteFacade::request('GET', 'https://ja.wikipedia.org/wiki/男子テニス選手一覧');

            $goutte->filter('ul')->each(function ($node) {
                $node->filter('li')->each(function ($li) {
                    // ノードが空の時のエラーハンドリング
                    if($li->filter('a')->count() > 0) {
                        echo 'text : ' . $li->text() . "\n";
                        echo 'link : ' . 'https://ja.wikipedia.org/' . $li->filter('a')->attr('href') . "\n";
                    }
                });
            });

            $this->logger->write('取得完了', 'info' ,true);
            $this->logger->success();
        } catch (Exception $e) {
            $this->logger->exception($e);
        }
        unset($this->logger);

        $this->info("実行終了");
    }
}
