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
    protected $signature = 'command:scrapeTourInfo {--sync : 同期処理}';
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
        $is_sync = $this->option('sync');

        $this->info("【実行開始】");
        $this->logger = new BatchLogger( 'scrapeTourInfo' );

        try {
            $node_all = array();

            $goutte = GoutteFacade::request('GET', 'https://news.tennis365.net/news/tour/schedule/');
            sleep(1);

            $goutte->filter('.scheBox td')->each(function ($node) use (&$node_all) {
                if ( $node->count() !== 0 ) {
                    array_push( $node_all, $node->text() );
                } else {
                    $this->info("スクレイピング実行できませんでした。");
                }
            });

            $tour_data = $this->makeInsertValue( $node_all );

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
        $this->info("【実行終了】");
    }


    /**
     * バルクインサートで保存用に加工する
     *
     * @param array $node_all
     * @return array
     */
    private function makeInsertValue( array $node_all ):array
    {
        $year = Carbon::now()->year;
        $today = Carbon::now();

        // 保存したいデータは6番目から始まる
        for ($i=6; $i<count($node_all); $i=$i+6) {

            // yyyy/m/dになるように加工
            $divided_date = explode('〜', $node_all[$i]);
            $start_date = $year . '/' . $divided_date[0];
            $end_date = $year . '/' . $divided_date[1];

            $tour_data[$i] = [
                'start_date' => $start_date ?? null,
                'end_date'   => $end_date ?? null,
                'year'       => $year,
                'name'       => $node_all[$i+1] ?? null,
                'category'   => $node_all[$i+2] ?? null,
                'location'   => $node_all[$i+3] ?? null,
                'surface'    => $node_all[$i+4] ?? null,
                'draw_num'   => (string) $node_all[$i+5] ?? null,
                'created_at' => $today,
                'updated_at' => $today,
            ];
        }
        return $tour_data;
    }
}
