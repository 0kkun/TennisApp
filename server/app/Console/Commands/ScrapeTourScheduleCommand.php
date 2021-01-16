<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Weidner\Goutte\GoutteFacade;
use App\Modules\BatchLogger;
use Exception;
use Carbon\Carbon;
use Symfony\Component\Console\Helper\ProgressBar;
use App\Repositories\Contracts\TourScheduleRepository;

class ScrapeTourScheduleCommand extends Command
{
    protected $signature = 'command:scrapeTourSchedule';
    protected $description = 'テニスの大会情報を公式サイトからスクレイピングするコマンド';

    const URL = 'https://www.atptour.com/en/tournaments';
    const LOOP_COUNT = 454;

    private $tour_schedule_repository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        TourScheduleRepository $tour_schedule_repository
    )
    {
        parent::__construct();
        $this->tour_schedule_repository = $tour_schedule_repository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("【実行開始】");
        $this->logger = new BatchLogger( 'scrapeTourSchedule' );

        try {
            $loop_count = self::LOOP_COUNT;
            $progress_bar = $this->output->createProgressBar($loop_count);

            //プログレスバー開始
            $progress_bar->start();

            // スクレイピング実行
            $results = $this->scrapeTourSchedule($progress_bar);
            $this->logger->write('スクレイピング実行完了', 'info' ,true);

            $results = $this->makeInsertValue($results);

            if ( !empty($results) ) {
                $this->tour_schedule_repository->bulkInsertOrUpdate($results);
                $this->logger->write(count($results) . '件、テーブル保存処理完了', 'info' ,true);
                $progress_bar->advance(100);
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
     * @return array
     */
    private function scrapeTourSchedule(ProgressBar $progress_bar): array
    {
        $data['name'] = [];
        $data['location'] = [];
        $data['term'] = [];
        $data['surface'] = [];
        $data['category'] = [];
        $goutte = GoutteFacade::request('GET', self::URL);
        sleep(1);

        $goutte->filter('.title-content > a')->each(function ($node) use (&$data, $progress_bar){
            if ( $node->count() > 0) {
                $data['name'][] = $node->text();
            } else {
                $this->info("スクレイピング実行できませんでした。");
                $this->logger->write('スクレイピングできませんでした。', 'info' ,true);
            }
            $progress_bar->advance(1);
        });

        $goutte->filter('.title-content > .tourney-location')->each(function ($node) use (&$data, $progress_bar){
            if ( $node->count() > 0) {
                $data['location'][] = $node->text();
            } else {
                $this->info("スクレイピング実行できませんでした。");
                $this->logger->write('スクレイピングできませんでした。', 'info' ,true);
            }
            $progress_bar->advance(1);
        });
        
        $goutte->filter('.title-content > .tourney-dates')->each(function ($node) use (&$data, $progress_bar){
            if ( $node->count() > 0) {
                $data['term'][] = $node->text();
            } else {
                $this->info("スクレイピング実行できませんでした。");
                $this->logger->write('スクレイピングできませんでした。', 'info' ,true);
            }
            $progress_bar->advance(1);
        });

        $data['start_date'] = $this->extractDate($data['term'], $is_start=true);
        $data['end_date'] = $this->extractDate($data['term'], $is_start=false);
        $data['year'] = $this->extractYear($data['start_date']);

        $goutte->filter('.tourney-details-table-wrapper')->each(function ($node) use (&$data, $progress_bar){
            if ( $node->count() > 0) {
                $data['surface'][] = $node->filter('.item-details')->eq(1)->text();
            } else {
                $this->info("スクレイピング実行できませんでした。");
                $this->logger->write('スクレイピングできませんでした。', 'info' ,true);
            }
            $progress_bar->advance(1);
        });

        $goutte->filter('.tourney-badge-wrapper > img')->each(function ($node) use (&$data, $progress_bar){
            if ( $node->count() > 0) {
                $data['category'][] = $node->attr('src');
            } else {
                $this->info("スクレイピング実行できませんでした。");
                $this->logger->write('スクレイピングできませんでした。', 'info' ,true);
            }
            $progress_bar->advance(1);
        });

        $data['category'] = $this->extractCategory($data);

        return $data;
    }

    /**
     * カテゴリー情報のみを抽出する
     *
     * @param array $string_arr
     * @return array
     */
    private function extractCategory(array $data): array
    {
        $is_occur_offset = false;

        foreach ( $data['category'] as $string ) {
            $start_pos = strpos($string, "_") + 1;
            $end_pos = strpos($string, ".");
            $extract_length = $end_pos - $start_pos;
            $results[] = substr($string, $start_pos, $extract_length);
        }

        for ($i=0; $i<count($data['name']); $i++) {
            // 「オリンピック」が含まれている場合は空白を代入
            if ( strpos($data['name'][$i],'Olympics') !== false ) {
                $category[] = '';
                $is_occur_offset = true; 
            // オフセットが発生していない場合はそのまま代入
            } elseif ( !$is_occur_offset ) {
                $category[] = $results[$i];
            // オフセットが発生している場合は一つずらす
            } elseif ( $is_occur_offset ) {
                $category[] = $results[$i -1];
            }
        }
        return $category;
    }


    /**
     * テキストの中にstart_dateとend_dateが入っているのでそれぞれ抽出する
     *
     * @param array $dates_arr
     * @return array
     */
    private function extractDate(array $dates_arr, bool $is_start): array
    {
        foreach ( $dates_arr as $dates ) {
            $str_length = strlen($dates);
            $start_pos = strpos($dates, " - ") + 1;
            $extract_length = $str_length - $start_pos;

            if ($is_start) $results[] = substr($dates, 0, $extract_length - 2);
            else $results[] = substr($dates, $start_pos + 2, $extract_length);
        }
        $results = str_replace($search='.', $replace='-', $results);

        return $results;
    }


    /**
     * $start_dateのテキスト中のyear情報を抽出する
     *
     * @param array $start_dates
     * @return array
     */
    private function extractYear(array $start_dates): array
    {
        foreach($start_dates as $start_date) {
            $results[] = substr($start_date, 0, 4);
        }
        return $results;
    }


    /**
     * テーブル保存用に加工する
     *
     * @param array $data
     * @return array
     */
    private function makeInsertValue(array $data): array
    {
        $now = Carbon::now();
        $values = [];

        for ($i=0; $i<count($data['name']); $i++) {
            $values[] = [
                'name'       => (string) $data['name'][$i],
                'location'   => (string) $data['location'][$i],
                'surface'    => (string) $data['surface'][$i],
                'category'   => (string) $data['category'][$i],
                'year'       => $data['year'][$i],
                'start_date' => $data['start_date'][$i],
                'end_date'   => $data['end_date'][$i],
                'updated_at' => $now,
                'created_at' => $now
            ];
        }
        return $values;
    }
}
