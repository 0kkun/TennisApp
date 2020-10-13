<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Weidner\Goutte\GoutteFacade;
use App\Modules\BatchLogger;
use Exception;
use Carbon\Carbon;
use App\Repositories\Contracts\AtpRankingsRepository;

class scrapeATPRanking extends Command
{

    protected $signature = 'command:scrapeATPRanking {--sync : 同期処理}';
    protected $description = 'ATPランキングをスクレイピングで取得し保存するコマンド';
    private $atp_rankings_repository;


    /**
     * リポジトリのコンストラクタ
     *
     * @param AtpRankingsRepository $atp_rankings_repository
     */
    public function __construct(
        AtpRankingsRepository $atp_rankings_repository
    )
    {
        parent::__construct();
        $this->atp_rankings_repository = $atp_rankings_repository;
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

        $is_sync = $this->option('sync');

        $this->info("実行開始");
        $this->logger = new BatchLogger( 'scrapeATPRanking' );

        try {
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

            $ranking_data = $this->makeInsertValue( $rank, $name, $country, $point );

            // バルクインサートで保存
            if ( !empty($ranking_data) ) {
                $this->atp_rankings_repository->bulkInsertOrUpdate($ranking_data);
            }

            $this->info("保存完了");
            $this->logger->write('保存完了', 'info' ,true);
            $this->logger->success();

        } catch (Exception $e) {
            $this->logger->exception($e);
        }
        unset($this->logger);

        $this->info("実行終了");
    }


    /**
     * レコード保存用のデータを作成。
     *
     * @param array $rank
     * @param array $name
     * @param array $country
     * @param array $point
     * @return array
     */
    private function makeInsertValue( array $rank, array $name, array $country, array $point ): array
    {
        $count = count( $rank );
        $today = Carbon::now();
        $ymd   = $today->copy()->format('Y-m-d');

        $point = $this->changeToNumeric( $point );

        // 前回のランキングを一括取得
        $pre_rankings_all = $this->atp_rankings_repository->getAll();

        for ( $i=0; $i<$count; $i++ ) {
            // 選手の前回のランキングを取得
            $pre_ranking = $pre_rankings_all->where('name', $name[$i])->first();

            $value[$i] = [
                'rank'       => (int) $rank[$i],
                'name'       => $name[$i],
                'country'    => $country[$i],
                'point'      => $point[$i],
                'ymd'        => $ymd,
                'created_at' => $today,
                'updated_at' => $today,
                'pre_rank'   => $pre_ranking['rank'] ?? null
            ];
        }
        return $value;
    }


    /**
     * カンマを削除するメソッド
     *
     * @param array $value
     * @return array
     */
    private function changeToNumeric( array $value ): array
    {
        $count = count( $value );

        for ($i=0; $i<$count; $i++) {
            $result[$i] = (int) str_replace( ',', '', $value[$i] );
        }

        return $result;
    }
}
