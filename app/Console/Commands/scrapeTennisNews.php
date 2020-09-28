<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Weidner\Goutte\GoutteFacade;
use App\Modules\BatchLogger;
use Exception;
use Carbon\Carbon;

class scrapeTennisNews extends Command
{

    protected $signature = 'command:scrapeTennisNews {--sync : 同期処理}';

    protected $description = 'テニスのニュースをスクレイピングで取得し保存するコマンド';

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
     * テニスニュースをスクレイピングで取得しDBへ保存する
     * TODO: 海外の記事も取得し、翻訳した物を保存できるようにしたい
     *
     * @return mixed
     */
    public function handle()
    {
        $is_sync = $this->option('sync'); 

        $this->logger = new BatchLogger( 'scrapeTennisNews' );

        $title = array();
        $url = array();
        $post_time = array();

        $goutte = GoutteFacade::request('GET', 'https://sports.yahoo.co.jp/news/list?id=tennis');
        sleep(1);

        $goutte->filter('.textNews')->each(function ($node) use (&$title, &$url, &$post_time) {
            if ( $node->count() > 0 ) {
                array_push( $title, $node->filter('.articleTitle')->text() );
                array_push( $url, $node->filter('.articleUrl')->attr('href') );
                array_push( $post_time, $node->filter('.postTime')->text() );
            } else {
                $this->info("スクレイピング実行できませんでした。");
            }
        });

        $tennis_news_data = $this->makeInsertValue( $title, $url, $post_time );

        dd($tennis_news_data);

    }


    /**
     * レコード保存用のデータを作成。
     *
     * @param array $title
     * @param array $news_url
     * @param array $post_time
     * @return array
     */
    private function makeInsertValue( array $title, array $url, array $post_time ): array
    {
        $count = count( $title );
        $today = Carbon::now();

        $post_time = $this->changeToCarbon( $post_time );

        for ( $i=0; $i<$count; $i++ ) {
            $value[$i] = [
                'title'      => $title[$i],
                'url'   => $url[$i],
                'post_time'  => $post_time[$i],
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
    private function changeToCarbon( array $post_time ): array
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
