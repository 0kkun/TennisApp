<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Weidner\Goutte\GoutteFacade;
use App\Modules\BatchLogger;
use Exception;
use Carbon\Carbon;
use Symfony\Component\Console\Helper\ProgressBar;
use App\Repositories\Contracts\PlayersNewsArticleRepository;

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

    private $players_news_article_repository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        PlayersNewsArticleRepository $players_news_article_repository
    )
    {
        parent::__construct();
        $this->players_news_article_repository = $players_news_article_repository;
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

            $results = $this->makeInsertValue($results);

            if ( !empty($results) ) {
                $this->players_news_article_repository->bulkInsertOrUpdate($results);
                $this->logger->write(count($results) . '件、テーブル保存処理完了', 'info' ,true);
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

            $goutte->filter('ul.articleList li.hasImg .articleListTtl')->each(function ($node) use (&$data, $progress_bar) {
                if ( $node->count() > 0) {
                    $data['title'][] = $node->text();
                } else {
                    $this->info("スクレイピング実行できませんでした。");
                    $this->logger->write('スクレイピングできませんでした。', 'info' ,true);
                }
                $progress_bar->advance(1);
            });

            $goutte->filter('ul.articleList .hasImg > a')->each(function ($node) use (&$data, $progress_bar) {
                if ( $node->count() > 0) {
                    $data['url'][] = $node->attr('href');
                } else {
                    $this->info("スクレイピング実行できませんでした。");
                    $this->logger->write('スクレイピングできませんでした。', 'info' ,true);
                }
                $progress_bar->advance(1);
            });

            $goutte->filter('ul.articleList .hasImg .articleListImg > img')->each(function ($node) use (&$data, $progress_bar) {
                if ( $node->count() > 0) {
                    $data['image'][] = $node->attr('src');
                } else {
                    $this->info("スクレイピング実行できませんでした。");
                    $this->logger->write('スクレイピングできませんでした。', 'info' ,true);
                }
                $progress_bar->advance(1);
            });

            $goutte->filter('ul.articleList .hasImg .articleListBody > time')->each(function ($node) use (&$data, $progress_bar) {
                if ( $node->count() > 0) {
                    $data['post_time'][] = $node->attr('datetime');
                } else {
                    $this->info("スクレイピング実行できませんでした。");
                    $this->logger->write('スクレイピングできませんでした。', 'info' ,true);
                }
                $progress_bar->advance(1);
            });

            $goutte->filter('ul.articleList .hasImg .articleListVender')->each(function ($node) use (&$data, $progress_bar) {
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


    /**
     * テーブル保存用に加工する
     *
     * @param array $data
     * @return array|Exception
     */
    private function makeInsertValue(array $data)
    {
        $value = [];
        $now = Carbon::now();

        $this->checkConflict($data);

        for ($i=0; $i < count($data['title']); $i++) {
            $value[$i] = [
                'title'      => (string) $data['title'][$i],
                'image'      => (string) $data['image'][$i] ?? '',
                'url'        => (string) $data['url'][$i] ?? '',
                'post_time'  => Carbon::parse($data['post_time'][$i]) ?? '',
                'vender'     => (string) $data['vender'][$i] ?? '',
                'created_at' => $now,
                'updated_at' => $now
            ];
        }
        return $value;
    }


    /**
     * データの齟齬をチェックする
     *
     * @param array $data
     * @return void
     */
    private function checkConflict(array $data)
    {
        $array = [
            count($data['title']),
            count($data['image']),
            count($data['url']),
            count($data['vender'])
        ];

        if (count(array_unique($array)) !== 1) {
            throw new Exception('データの齟齬が発生しています！');
        }
    }
}
