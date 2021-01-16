<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Weidner\Goutte\GoutteFacade;
use App\Modules\BatchLogger;
use Exception;
use Carbon\Carbon;
use Symfony\Component\Console\Helper\ProgressBar;

class ScrapeTourScheduleCommand extends Command
{
    protected $signature = 'command:scrapeTourSchedule';
    protected $description = 'テニスの大会情報を公式サイトからスクレイピングするコマンド';

    const URL = 'https://www.atptour.com/en/tournaments';
    const LOOP_COUNT = 14121;

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("【実行開始】");
        $this->logger = new BatchLogger( 'scrapeRanking' );

        // try {
            $loop_count = self::LOOP_COUNT;
            $progress_bar = $this->output->createProgressBar($loop_count);

            //プログレスバー開始
            $progress_bar->start();

            // スクレイピング実行
            $results = $this->scrapeTourSchedule($progress_bar);
            $this->logger->write('スクレイピング実行完了', 'info' ,true);

            dd($results);

            if ( !empty($results) ) {
                // $this->ranking_repository->bulkInsertOrUpdate($results);
                $this->logger->write('テーブル保存処理完了', 'info' ,true);
                $progress_bar->advance(100);
            }

            //プログレスバー終了
            $progress_bar->finish();
            $this->logger->success();

        // } catch (Exception $e) {
        //     $this->logger->exception($e);
        // }
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

        $data['start_date'][] = $this->extractDate($data['term'], $is_start=true);
        $data['end_date'][] = $this->extractDate($data['term'], $is_start=false);

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

        $data['category'] = $this->extractCategory($data['category']);

        return $data;
    }

    /**
     * カテゴリー情報のみを抽出する
     *
     * @param array $string_arr
     * @return array
     */
    private function extractCategory(array $strings): array
    {
        foreach ( $strings as $string ) {
            $start_pos = strpos($string, "_") + 1;
            $end_pos = strpos($string, ".");
            $extract_length = $end_pos - $start_pos;
            $results[] = substr($string, $start_pos, $extract_length);
        }
        return $results;
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
}
