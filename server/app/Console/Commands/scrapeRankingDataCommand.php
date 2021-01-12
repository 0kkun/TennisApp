<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Weidner\Goutte\GoutteFacade;
use App\Modules\BatchLogger;
use Exception;
use Carbon\Carbon;

class scrapeRankingDataCommand extends Command
{
    protected $signature = 'command:scrapeRanking';
    protected $description = 'テニスのランキングデータをスクレイピングで取得するコマンド';

    const URL = 'https://live-tennis.eu/en/atp-live-ranking';
    const CHUNK_SIZE = 14;
    const LOOP_COUNT = 14021;


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
     * -- 処理の流れ --
     * arrayの最初から検索し、順位を検索、位置を検出
     * advertisementなどの余計な文字の配列をキーごと削除する
     * テーブル保存用に加工する
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("【実行開始】");
        $this->logger = new BatchLogger( 'scrapeRanking' );

        try {
            $loop_count = self::LOOP_COUNT;
            $progress_bar = $this->output->createProgressBar($loop_count);

            //プログレスバー開始
            $progress_bar->start();

            // スクレイピング実行
            $scrape_row_results = $this->scrapeRanking($progress_bar);

            // 余分なテキストを削除する
            $results = $this->excludeText($scrape_row_results);

            // 選手ごとにchunkする
            $results = array_chunk($results, self::CHUNK_SIZE);

            // テーブル保存用に加工
            $results = $this->makeInsertValue($results);

            //プログレスバー終了
            $progress_bar->finish();
            $this->logger->write('保存完了', 'info' ,true);
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
    private function scrapeRanking($progress_bar): array
    {
        $data = [];
        $goutte = GoutteFacade::request('GET', self::URL);
        sleep(1);

        $goutte->filter('tbody tr td')->each(function ($node) use (&$data, $progress_bar){
            if ( $node->count() > 0) {
                array_push($data, $node->text());
            } else {
                $this->info("スクレイピング実行できませんでした。");
                $this->logger->write('スクレイピングできませんでした。', 'info' ,true);
            }
            $progress_bar->advance(1);
        });

        return $data;
    }


    /**
     * 余分なテキストを排除する
     *
     * @param array $data
     * @return array
     */
    private function excludeText(array $data): array
    {
        $results = [];
        $blank_pattern = '/\A[\p{C}\p{Z}]++|[\p{C}\p{Z}]++\z/u';
        $exclude_text = ["Advertisement", "Advertisement (adsbygoogle = window.adsbygoogle || []).push({});"];

        foreach ( $data as $datum ) {
            // マルチバイト文字の空白を削除する
            $blank_removed = preg_replace($blank_pattern, '', $datum);
            // 配列に取得したスクレイピングデータを追加
            array_push($results, $blank_removed);
        }

        // 余計なテキストが含まれている場合はキーごと削除する
        $results = array_diff($results, $exclude_text);

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
        $today = Carbon::now();

        foreach ($data as $index => $datum) {
            if (!empty($datum[0])) {
                // CHなら現在のランクにする
                $most_heighest_rank = $datum[1] == 'CH' ? $datum[0] : $datum[1];
                // NCHなら空欄にする。型までチェックする必要があるため、!==falseを使うこと
                if ( strpos($datum[1], "NCH") !== false ) $most_heighest_rank = null;

                $formated_data[$index] = [
                    'rank'                => (int) $datum[0],
                    'most_highest'        => (int) $most_heighest_rank ?? null,
                    'name'                => $this->transliterateString($datum[3]),
                    'age'                 => (int) $datum[4],
                    'country'             => substr($datum[5], 0, 3),
                    'point'               => (int) $datum[6],
                    'rank_change'         => (int) $datum[7] ?? 0,
                    'point_change'        => (int) $datum[8] ?? 0,
                    'current_tour_result' => $datum[9],
                    'pre_tour_result'     => $datum[10],
                    'next_point'          => (int) $datum[12] ?? 0,
                    'max_point'           => (int) $datum[13] ?? 0,
                    'created_at'          => $today,
                    'updated_at'          => $today
                ];
            }
        }
        return $formated_data;
    }


    /**
     * 外国の文字をローマの同等のものに変換する
     * キリル文字も変換する
     *
     * @param string $txt
     * @return string
     */
    private function transliterateString(string $txt): string
    {
        $transliterationTable = array('á' => 'a', 'Á' => 'A', 'à' => 'a', 'À' => 'A', 'ă' => 'a', 'Ă' => 'A', 'â' => 'a', 'Â' => 'A', 'å' => 'a', 'Å' => 'A', 'ã' => 'a', 'Ã' => 'A', 'ą' => 'a', 'Ą' => 'A', 'ā' => 'a', 'Ā' => 'A', 'ä' => 'ae', 'Ä' => 'AE', 'æ' => 'ae', 'Æ' => 'AE', 'ḃ' => 'b', 'Ḃ' => 'B', 'ć' => 'c', 'Ć' => 'C', 'ĉ' => 'c', 'Ĉ' => 'C', 'č' => 'c', 'Č' => 'C', 'ċ' => 'c', 'Ċ' => 'C', 'ç' => 'c', 'Ç' => 'C', 'ď' => 'd', 'Ď' => 'D', 'ḋ' => 'd', 'Ḋ' => 'D', 'đ' => 'd', 'Đ' => 'D', 'ð' => 'dh', 'Ð' => 'Dh', 'é' => 'e', 'É' => 'E', 'è' => 'e', 'È' => 'E', 'ĕ' => 'e', 'Ĕ' => 'E', 'ê' => 'e', 'Ê' => 'E', 'ě' => 'e', 'Ě' => 'E', 'ë' => 'e', 'Ë' => 'E', 'ė' => 'e', 'Ė' => 'E', 'ę' => 'e', 'Ę' => 'E', 'ē' => 'e', 'Ē' => 'E', 'ḟ' => 'f', 'Ḟ' => 'F', 'ƒ' => 'f', 'Ƒ' => 'F', 'ğ' => 'g', 'Ğ' => 'G', 'ĝ' => 'g', 'Ĝ' => 'G', 'ġ' => 'g', 'Ġ' => 'G', 'ģ' => 'g', 'Ģ' => 'G', 'ĥ' => 'h', 'Ĥ' => 'H', 'ħ' => 'h', 'Ħ' => 'H', 'í' => 'i', 'Í' => 'I', 'ì' => 'i', 'Ì' => 'I', 'î' => 'i', 'Î' => 'I', 'ï' => 'i', 'Ï' => 'I', 'ĩ' => 'i', 'Ĩ' => 'I', 'į' => 'i', 'Į' => 'I', 'ī' => 'i', 'Ī' => 'I', 'ĵ' => 'j', 'Ĵ' => 'J', 'ķ' => 'k', 'Ķ' => 'K', 'ĺ' => 'l', 'Ĺ' => 'L', 'ľ' => 'l', 'Ľ' => 'L', 'ļ' => 'l', 'Ļ' => 'L', 'ł' => 'l', 'Ł' => 'L', 'ṁ' => 'm', 'Ṁ' => 'M', 'ń' => 'n', 'Ń' => 'N', 'ň' => 'n', 'Ň' => 'N', 'ñ' => 'n', 'Ñ' => 'N', 'ņ' => 'n', 'Ņ' => 'N', 'ó' => 'o', 'Ó' => 'O', 'ò' => 'o', 'Ò' => 'O', 'ô' => 'o', 'Ô' => 'O', 'ő' => 'o', 'Ő' => 'O', 'õ' => 'o', 'Õ' => 'O', 'ø' => 'oe', 'Ø' => 'OE', 'ō' => 'o', 'Ō' => 'O', 'ơ' => 'o', 'Ơ' => 'O', 'ö' => 'oe', 'Ö' => 'OE', 'ṗ' => 'p', 'Ṗ' => 'P', 'ŕ' => 'r', 'Ŕ' => 'R', 'ř' => 'r', 'Ř' => 'R', 'ŗ' => 'r', 'Ŗ' => 'R', 'ś' => 's', 'Ś' => 'S', 'ŝ' => 's', 'Ŝ' => 'S', 'š' => 's', 'Š' => 'S', 'ṡ' => 's', 'Ṡ' => 'S', 'ş' => 's', 'Ş' => 'S', 'ș' => 's', 'Ș' => 'S', 'ß' => 'SS', 'ť' => 't', 'Ť' => 'T', 'ṫ' => 't', 'Ṫ' => 'T', 'ţ' => 't', 'Ţ' => 'T', 'ț' => 't', 'Ț' => 'T', 'ŧ' => 't', 'Ŧ' => 'T', 'ú' => 'u', 'Ú' => 'U', 'ù' => 'u', 'Ù' => 'U', 'ŭ' => 'u', 'Ŭ' => 'U', 'û' => 'u', 'Û' => 'U', 'ů' => 'u', 'Ů' => 'U', 'ű' => 'u', 'Ű' => 'U', 'ũ' => 'u', 'Ũ' => 'U', 'ų' => 'u', 'Ų' => 'U', 'ū' => 'u', 'Ū' => 'U', 'ư' => 'u', 'Ư' => 'U', 'ü' => 'ue', 'Ü' => 'UE', 'ẃ' => 'w', 'Ẃ' => 'W', 'ẁ' => 'w', 'Ẁ' => 'W', 'ŵ' => 'w', 'Ŵ' => 'W', 'ẅ' => 'w', 'Ẅ' => 'W', 'ý' => 'y', 'Ý' => 'Y', 'ỳ' => 'y', 'Ỳ' => 'Y', 'ŷ' => 'y', 'Ŷ' => 'Y', 'ÿ' => 'y', 'Ÿ' => 'Y', 'ź' => 'z', 'Ź' => 'Z', 'ž' => 'z', 'Ž' => 'Z', 'ż' => 'z', 'Ż' => 'Z', 'þ' => 'th', 'Þ' => 'Th', 'µ' => 'u', 'а' => 'a', 'А' => 'a', 'б' => 'b', 'Б' => 'b', 'в' => 'v', 'В' => 'v', 'г' => 'g', 'Г' => 'g', 'д' => 'd', 'Д' => 'd', 'е' => 'e', 'Е' => 'E', 'ё' => 'e', 'Ё' => 'E', 'ж' => 'zh', 'Ж' => 'zh', 'з' => 'z', 'З' => 'z', 'и' => 'i', 'И' => 'i', 'й' => 'j', 'Й' => 'j', 'к' => 'k', 'К' => 'k', 'л' => 'l', 'Л' => 'l', 'м' => 'm', 'М' => 'm', 'н' => 'n', 'Н' => 'n', 'о' => 'o', 'О' => 'o', 'п' => 'p', 'П' => 'p', 'р' => 'r', 'Р' => 'r', 'с' => 's', 'С' => 's', 'т' => 't', 'Т' => 't', 'у' => 'u', 'У' => 'u', 'ф' => 'f', 'Ф' => 'f', 'х' => 'h', 'Х' => 'h', 'ц' => 'c', 'Ц' => 'c', 'ч' => 'ch', 'Ч' => 'ch', 'ш' => 'sh', 'Ш' => 'sh', 'щ' => 'sch', 'Щ' => 'sch', 'ъ' => '', 'Ъ' => '', 'ы' => 'y', 'Ы' => 'y', 'ь' => '', 'Ь' => '', 'э' => 'e', 'Э' => 'e', 'ю' => 'ju', 'Ю' => 'ju', 'я' => 'ja', 'Я' => 'ja');
        return str_replace(array_keys($transliterationTable), array_values($transliterationTable), $txt);
    }
}

