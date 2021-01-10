<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Weidner\Goutte\GoutteFacade;
use App\Modules\BatchLogger;
use Exception;
use Carbon\Carbon;
use App\Repositories\Contracts\TourInformationsRepository;

class scrapeTourInfo extends Command
{
    protected $signature = 'command:scrapeTourInfo';
    protected $description = '大会情報をスクレイピングで取得し保存する';
    private $tour_informations_repository;


    /**
     * リポジトリのコンストラクタ
     *
     * @param TourInformationsRepository $tour_informations_repository
     */
    public function __construct(
        TourInformationsRepository $tour_informations_repository
    )
    {
        parent::__construct();
        $this->tour_informations_repository = $tour_informations_repository;
    }


    /**
     * 大会情報をスクレイピングで取得し、テーブルへ保存する
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("実行開始");
        $this->logger = new BatchLogger( 'scrapeTourInfo' );

        try {
            $site_url = 'https://news.tennis365.net/news/tour/schedule/';

            $scrape_data = $this->execScrape( $site_url );

            $tour_data = $this->makeInsertValue( $scrape_data );

            // バルクインサートで保存
            if ( !empty($tour_data) ) {
                $this->tour_informations_repository->bulkInsertOrUpdate( $tour_data );
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
     * スクレイピング実行
     *
     * @param string $site_url
     * @return array
     */
    private function execScrape( string $site_url ): array
    {
        $data = array();

        $goutte = GoutteFacade::request('GET', $site_url);
        sleep(1);

        $goutte->filter('.scheBox td')->each(function ($node) use (&$data) {
            if ( $node->count() !== 0 ) {
                array_push( $data, $node->text() );
            } else {
                $this->info("スクレイピング実行できませんでした。");
            }
        });
        return $data;
    }


    /**
     * バルクインサートで保存用に加工する
     *
     * @param array $data
     * @return array
     */
    private function makeInsertValue( array $data ):array
    {
        $year = Carbon::now()->year;
        $today = Carbon::now();

        // 保存したいデータは6番目から始まる
        for ($i=6; $i<count($data); $i=$i+6) {

            // yyyy/m/dになるように加工
            $divided_date = explode('〜', $data[$i]);
            $start_date = $year . '/' . $divided_date[0];
            $end_date = $year . '/' . $divided_date[1];

            $tour_data[$i] = [
                'start_date' => $start_date ?? null,
                'end_date'   => $end_date ?? null,
                'year'       => $year,
                'name'       => $data[$i+1] ?? null,
                'category'   => $data[$i+2] ?? null,
                'location'   => $data[$i+3] ?? null,
                'surface'    => $data[$i+4] ?? null,
                'draw_num'   => (string) $data[$i+5] ?? null,
                'created_at' => $today,
                'updated_at' => $today,
            ];
        }
        return $tour_data;
    }
}
