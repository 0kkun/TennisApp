<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Modules\BatchLogger;
use Exception;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Weidner\Goutte\GoutteFacade;
use App\Repositories\Contracts\NewsArticlesRepository;

class ScrapeTennisNewsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $news_articles_repository;

    public function __construct()
    {
    }


    /**
     * テニスニュースをスクレイピングで取得しDBへ保存する
     * TODO: 海外の記事も取得し、翻訳した物を保存できるようにしたい
     * FIXME: 2回連続で実行時にupdateする際、updated_atのtimezoneがアジアではなくなるバグ有り
     * 
     * 実行ログ
     * [2020-10-19 03:43:44] local.INFO: [ScrapeTennisNewsJob] 保存完了 : 5.5461130142212秒  
     * [2020-10-19 03:43:44] local.INFO: [ END ] ScrapeTennisNewsJob 処理時間: 5.5464880466461秒
     *
     * @return mixed
     */
    public function handle(
        NewsArticlesRepository $news_articles_repository
    )
    {
        $this->news_articles_repository = $news_articles_repository;

        $this->logger = new BatchLogger('ScrapeTennisNewsJob');

        try {
            $scrape_data = [
                'title'     => [],
                'url'       => [],
                'post_time' => [],
            ];

            $yahoo_url_page1 = 'https://sports.yahoo.co.jp/news/list?id=tennis';
            $yahoo_url_page2 = 'https://sports.yahoo.co.jp/news/list?id=tennis&fmi=tennis&p=2';
            $yahoo_url_page3 = 'https://sports.yahoo.co.jp/news/list?id=tennis&fmi=tennis&p=3';

            // スクレイピング時のhtmlクラスを格納
            $pattern = [
                'all'       => '.textNews',
                'title'     => '.articleTitle',
                'url'       => '.articleUrl',
                'post_time' => '.postTime'
            ];

            // スクレイピング実行
            $this->scrapeTennisNewsSite( $yahoo_url_page1, $pattern, $scrape_data );
            $this->scrapeTennisNewsSite( $yahoo_url_page2, $pattern, $scrape_data );
            $article_data = $this->scrapeTennisNewsSite( $yahoo_url_page3, $pattern, $scrape_data );

            // バルクインサートで保存
            if ( !empty($article_data) ) {
                $this->news_articles_repository->bulkInsertOrUpdate( $article_data );
            }

            $this->logger->write('保存完了', 'info' ,true);
            $this->logger->success();

        } catch (Exception $e) {
            $this->logger->exception($e);
        }
        unset($this->logger);
    }



    /**
     * テニスニュースサイトのスクレイピング実行
     *
     * @param string $site_url
     * @param array $pattern
     * @param array $data
     * @return array
     */
    private function scrapeTennisNewsSite( string $site_url, array $pattern, array &$data ): array
    {
        $goutte = GoutteFacade::request('GET', $site_url);
        sleep(1);

        $goutte->filter($pattern['all'])->each(function ($node) use (&$pattern, &$data) {
            if ( $node->count() > 0 ) {
                array_push( $data['title'], $node->filter($pattern['title'])->text() );
                array_push( $data['url'], $node->filter($pattern['url'])->attr('href') );
                array_push( $data['post_time'], $node->filter($pattern['post_time'])->text() );
            } else {
                Log::info('スクレイピング実行できませんでした');
            }
        });

        $tennis_news_data = $this->makeInsertValue( $data );

        return $tennis_news_data;
    }


    /**
     * レコード保存用のデータを作成。
     *
     * @param array $data['title']
     * @param array $news_url
     * @param array $post_time
     * @return array
     */
    private function makeInsertValue( array $data ): array
    {
        $count = count( $data['title'] );
        $today = Carbon::now();

        $data['post_time'] = $this->formatToCarbon( $data['post_time'] );

        for ( $i=0; $i<$count; $i++ ) {
            $value[$i] = [
                'title'      => $data['title'][$i],
                'url'        => $data['url'][$i],
                'post_time'  => $data['post_time'][$i],
                'created_at' => $today,
                'updated_at' => $today
            ];
        }
        return $value;
    }


    /**
     * ニュース記事投稿日をCarbonに変換する
     *
     * @param array $post_time
     * @return array
     */
    private function formatToCarbon( array $post_time ): array
    {
        $count = count( $post_time );

        for ($i=0; $i<$count; $i++) {
            // 全角空白が混じっているので置換
            $post_time[$i] = str_replace(' ', ' ', $post_time[$i]);
            // Carbon生成
            $replaced_post_time[$i] = Carbon::createFromTimeString($post_time[$i]);
        }
        return $replaced_post_time;
        
    }
}
