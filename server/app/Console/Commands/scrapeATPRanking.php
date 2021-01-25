<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Weidner\Goutte\GoutteFacade;
use App\Modules\BatchLogger;
use Exception;
use Carbon\Carbon;
use App\Repositories\Contracts\AtpRankingsRepository;

/**
 * MEMO: 現在は使用していない
 */
class scrapeATPRanking extends Command
{

    protected $signature = 'command:scrapeATPRanking';
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
     * ATPランキング情報を取得する。
     * 2ページ(1~50位と51~100位)に分かれているのでスクレイピングは2回実行している
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("実行開始");
        $this->logger = new BatchLogger( 'scrapeATPRanking' );

        try {
            // 各キーに配列を設定
            $scrape_data['rank']    = array();
            $scrape_data['name']    = array();
            $scrape_data['country'] = array();
            $scrape_data['point']   = array();

            $site_url_first = 'https://sportsnavi.ht.kyodo-d.jp/tennis/ranking/atp/point/';
            $site_url_second = $site_url_first . '?p=2';

            $this->execScrape( $site_url_first, $scrape_data );
            $this->execScrape( $site_url_second, $scrape_data );

            $atp_ranking_data = $this->makeInsertValue( $scrape_data );

            // バルクインサートで保存
            if ( !empty($atp_ranking_data) ) {
                $this->atp_rankings_repository->bulkInsertOrUpdate($atp_ranking_data);
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
     * スクレイピング実行メソッド
     * 参照渡しにすることですでに格納されているデータに追加していける
     *
     * @param string $site_url
     * @param array $data
     * @return array
     */
    private function execScrape( string $site_url, array &$data ):array
    {
        $goutte = GoutteFacade::request('GET', $site_url);
        sleep(1);

        $goutte->filter('tbody tr')->each(function ($node) use (&$data){
            if ( $node->count() > 0 ) {
                array_push( $data['rank'], $node->filter('td')->eq(0)->text() );
                array_push( $data['name'], $node->filter('td')->eq(1)->text() );
                array_push( $data['country'], $node->filter('td')->eq(2)->text() );
                array_push( $data['point'], $node->filter('td')->eq(3)->text() );
            } else {
                $this->info("スクレイピング実行できませんでした。");
            }
        });
        return $data;
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
    private function makeInsertValue( array $scraped_data ): array
    {
        $count = count( $scraped_data['rank'] );

        $today = Carbon::now();
        $ymd   = $today->copy()->format('Y-m-d');

        $scraped_data['point'] = $this->changeToNumeric( $scraped_data['point'] );

        // 前回のランキングを一括取得
        $pre_rankings_all = $this->atp_rankings_repository->getAll();

        for ( $i=0; $i<$count; $i++ ) {
            // 選手の前回のランキングを取得
            $pre_ranking = $pre_rankings_all->where('name', $scraped_data['name'][$i])->first();

            $value[$i] = [
                'rank'       => $scraped_data['rank'][$i],
                'name'       => $scraped_data['name'][$i],
                'country'    => $scraped_data['country'][$i],
                'point'      => $scraped_data['point'][$i],
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
