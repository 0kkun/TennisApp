<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Weidner\Goutte\GoutteFacade;
use App\Modules\BatchLogger;
use Exception;
use Carbon\Carbon;
use Symfony\Component\Console\Helper\ProgressBar;

class ScrapeNewsCommand extends Command
{
    protected $signature = 'command:scrapeNews';

    protected $description = 'テニスのニュースをスクレイピングするコマンド';

    const URL = [
        'https://news.livedoor.com/article/category/527/',
        'https://news.livedoor.com/article/category/527/?p=2',
        'https://news.livedoor.com/article/category/527/?p=3',
        'https://news.livedoor.com/article/category/527/?p=4',
        'https://news.livedoor.com/article/category/527/?p=5',
        'https://news.livedoor.com/article/category/527/?p=6',
        'https://news.livedoor.com/article/category/527/?p=7'
    ];
    const LOOP_COUNT = 701;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        $this->info("【実行開始】");
        $this->logger = new BatchLogger( 'scrapeNews' );


        try {
            $loop_count = self::LOOP_COUNT;
            $progress_bar = $this->output->createProgressBar($loop_count);

            //プログレスバー開始
            $progress_bar->start();

            // スクレイピング実行
            $results = $this->scrapeNews($progress_bar);
            $this->logger->write('スクレイピング実行完了', 'info' ,true);

            if ( !empty($results) ) {
                // $this->ranking_repository->bulkInsertOrUpdate($results);
                $this->logger->write('テーブル保存処理完了', 'info' ,true);
                $progress_bar->advance(1);
            }

            //プログレスバー終了
            $progress_bar->finish();
            $this->logger->success();

        } catch (Exception $e) {
            $this->logger->exception($e);
        }
        unset($this->logger);

        $this->info("\n" . "【実行終了】");
    }


    /**
     * スクレイピングを実行する
     *
     * @param ProgressBar $progress_bar
     * @return array
     */
    private function scrapeNews(ProgressBar $progress_bar): array
    {
        $data['title'] = [];
        $data['image'] = [];
        $data['url'] = [];
        $data['post_time'] = [];
        $data['vender'] = [];

        for ( $i=0; $i<count(self::URL); $i++ ) {
            $goutte = GoutteFacade::request('GET', self::URL[$i]);
            sleep(1);

            $goutte->filter('.articleListBody .articleListTtl')->each(function ($node) use (&$data, $progress_bar) {
                if ( $node->count() > 0) {
                    $data['title'][] = $node->text();
                } else {
                    $this->info("スクレイピング実行できませんでした。");
                    $this->logger->write('スクレイピングできませんでした。', 'info' ,true);
                }
                $progress_bar->advance(1);
            });

            $goutte->filter('.hasImg > a')->each(function ($node) use (&$data, $progress_bar) {
                if ( $node->count() > 0) {
                    $data['url'][] = $node->attr('href');
                } else {
                    $this->info("スクレイピング実行できませんでした。");
                    $this->logger->write('スクレイピングできませんでした。', 'info' ,true);
                }
                $progress_bar->advance(1);
            });

            $goutte->filter('.articleListImg > img')->each(function ($node) use (&$data, $progress_bar) {
                if ( $node->count() > 0) {
                    $data['image'][] = $node->attr('src');
                } else {
                    $this->info("スクレイピング実行できませんでした。");
                    $this->logger->write('スクレイピングできませんでした。', 'info' ,true);
                }
                $progress_bar->advance(1);
            });

            $goutte->filter('.articleListBody > time')->each(function ($node) use (&$data, $progress_bar) {
                if ( $node->count() > 0) {
                    $data['post_time'][] = $node->attr('datetime');
                } else {
                    $this->info("スクレイピング実行できませんでした。");
                    $this->logger->write('スクレイピングできませんでした。', 'info' ,true);
                }
                $progress_bar->advance(1);
            });

            $goutte->filter('.articleListVender')->each(function ($node) use (&$data, $progress_bar) {
                if ( $node->count() > 0) {
                    $data['vender'][] = $node->text();
                } else {
                    $this->info("スクレイピング実行できませんでした。");
                    $this->logger->write('スクレイピングできませんでした。', 'info' ,true);
                }
                $progress_bar->advance(1);
            });
        }
        return $data;
    }
}
