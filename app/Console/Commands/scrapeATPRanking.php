<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Weidner\Goutte\GoutteFacade;
use App\Modules\BatchLogger;
use Exception;
use Carbon\Carbon;

class scrapeATPRanking extends Command
{

    protected $signature = 'command:scrapeATPRanking {--sync : 同期処理}';

    protected $description = 'ATPランキングをスクレイピングで取得するコマンド';

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
     * ランキング情報を取得する。
     * 1~50位と50~100位が2ページに分かれているのでスクレイピングは2回実行している
     *
     * @return mixed
     */
    public function handle()
    {
        /**
         * MEMO:
         * ->getUri()       最後にたどり着いたURL
         * ->text()         テキストのみ取得
         * ->first()        一番最初のを取得
         * ->html()         HTMLを取得
         * ->attr('href')   属性を取得
         * ->eq(0)          同一タグがある場合、登場順番を指定できる
         */

        $this->info("実行開始");

        $rank = array();
        $name = array();
        $country = array();
        $point = array();

        $goutte_part1 = GoutteFacade::request('GET', 'https://sportsnavi.ht.kyodo-d.jp/tennis/ranking/atp/point/');
        sleep(1);
        $goutte_part2 = GoutteFacade::request('GET', 'https://sportsnavi.ht.kyodo-d.jp/tennis/ranking/atp/point/?p=2');
        sleep(1);

        // 1 ~ 50位のランキングを取得
        $goutte_part1->filter('tbody tr')->each(function ($node) use (&$rank, &$name, &$country, &$point){
            if ( $node->count() > 0 ) {
                array_push( $rank, $node->filter('td')->eq(0)->text() );
                array_push( $name, $node->filter('td')->eq(1)->text() );
                array_push( $country, $node->filter('td')->eq(2)->text() );
                array_push( $point, $node->filter('td')->eq(3)->text() );
            } else {
                $this->info("スクレイピング実行できませんでした。");
            }
        });

        // 50 ~ 100位のランキングを取得
        $goutte_part2->filter('tbody tr')->each(function ($node) use (&$rank, &$name, &$country, &$point){
            if ( $node->count() > 0 ) {
                array_push( $rank, $node->filter('td')->eq(0)->text() );
                array_push( $name, $node->filter('td')->eq(1)->text() );
                array_push( $country, $node->filter('td')->eq(2)->text() );
                array_push( $point, $node->filter('td')->eq(3)->text() );
            } else {
                $this->info("スクレイピング実行できませんでした。");
            }
        });

        $ranking_data = $this->makeInsertValue( $rank,$name,$country,$point );

    }


    private function makeInsertValue( array $rank, array $name, array $country, array $point ): array
    {
        $count = count( $rank );
        $today = Carbon::now();

        for ( $i=0; $i<$count; $i++ ) {
            $value[$i] = [
                'rank'    => $rank[$i],
                'name'    => $name[$i],
                'country'    => $country[$i],
                'point'        => $point[$i],
                'created_at' => $today,
                'updated_at' => $today
            ];
        }
        return $value;
    }
}
