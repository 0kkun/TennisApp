<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Contracts\PlayersRepository;
use App\Repositories\Contracts\YoutubeVideosRepository;
use Carbon\Carbon;
use Google_Client;
use Google_Service_YouTube;
use Google_Service_Exception;
use Google_Exception;
use App\Modules\BatchLogger;
use Exception;

class getPlayersYoutube extends Command
{
    protected $signature = 'command:getPlayersYoutube';
    protected $description = '選手のyoutube動画を取得する';

    private $players_repository;
    private $youtube_videos_repository;

    const MAX_COUNT = 5;

    /**
     * リポジトリのコンストラクタ
     *
     * @param PlayersRepository $players_repository
     * @param YoutubeVideosRepository $youtube_videos_repository
     */
    public function __construct(
        PlayersRepository $players_repository,
        YoutubeVideosRepository $youtube_videos_repository
    )
    {
        parent::__construct();
        $this->players_repository = $players_repository;
        $this->youtube_videos_repository = $youtube_videos_repository;
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info( "【実行開始】" );
        $this->logger = new BatchLogger( 'getPlayersYoutube' );

        try {
            // Googleへの接続情報のインスタンスを作成と設定
            $client = new Google_Client();

            // $client->setDeveloperKey(config('app.google_api_key1'));
            // $client->setDeveloperKey(config('app.google_api_key2')); // クォータが尽きた時用の予備
            // $client->setDeveloperKey(config('app.google_api_key3')); // クォータが尽きた時用の予備
            $client->setDeveloperKey(config('app.google_api_key4')); // クォータが尽きた時用の予備

            $youtube = new Google_Service_YouTube($client);

            // 日付はRFC3339形式にしないと読み込め無いのでフォーマット
            $one_week_ago = strtotime('-7 day');
            $search_duration = date(DATE_RFC3339, $one_week_ago);

            $players = $this->players_repository->getActivePlayers()->toArray();

            $year = Carbon::today()->year;

            for ( $i=0; $i<count($players); $i++ ) {
                $check_word = $players[$i]['name_en'];
                $keyword = $players[$i]['name_en'] . " " . $year;
                $player_id = $players[$i]['id'];
                $this->info( $check_word . 'の動画取得開始' );
                $videos = $this->getYoutube( $youtube, $search_duration, $keyword );
                sleep(0.5);

                $video_data = $this->makeInsertValue( $videos, $player_id, $check_word );

                if( !empty($video_data) ){
                    $this->youtube_videos_repository->bulkInsertOrUpdate($video_data);
                    $this->info( $i . "件目 登録完了" );
                } else {
                    $this->info( $check_word . 'は該当動画無し' );
                }
            }
            $this->logger->write('保存終了', 'info' ,true);
            $this->logger->success();
            
        } catch (Exception $e) {
            $this->logger->exception($e);
        }
        unset($this->logger);
        $this->info( "【実行終了】" );
    }


    /**
     * youtube api v3 で動画取得
     *
     * @param [type] $youtube
     * @param [type] $search_duration
     * @param string $keyword
     * @return array
     */
    private function getYoutube( $youtube, $search_duration, string $keyword ): array
    {
        $videos = [];

        // リファレンス：https://developers.google.com/youtube/v3/docs/search/list?hl=ja
        $params = [
            'q'                 => $keyword,
            'type'              => 'video',
            'order'             => 'relevance',         // 関連性が高い順
            'maxResults'        => self::MAX_COUNT,
            'videoEmbeddable'   => true,                // 埋め込み可能な動画のみに制限
            'publishedAfter'    => $search_duration,    // この日以降にアップされた動画を取得
            'videoDuration'     => 'medium',            // 4-20分の動画のみに制限
        ];

        try {
            $searchResponse = $youtube->search->listSearch('snippet', $params);
            array_map(function ($searchResult) use (&$videos) {
                $videos[] = $searchResult;
            }, $searchResponse['items']);

        } catch (Google_Service_Exception $e) {
            echo htmlspecialchars($e->getMessage());
            exit;
        } catch (Google_Exception $e) {
            echo htmlspecialchars($e->getMessage());
            exit;
        }

        return $videos;
    }


    /**
     * バルクインサート用に加工
     *
     * @param array $videos
     * @param integer $player_id
     * @param string $check_word
     * @return array
     */
    public function makeInsertValue( array $videos, int $player_id, string $check_word ): array
    {
        $today = Carbon::now();
        $count = count($videos);
        $value = array();

        for ( $i=0; $i<$count; $i++ ) {
            if ( stripos($videos[$i]['snippet']['title'], $check_word) !== false ) {
                $value[$i] = [
                    'title'      => $videos[$i]['snippet']['title'],
                    'url'        => 'https://www.youtube.com/embed/' . $videos[$i]['id']['videoId'],
                    // TODO: RFC3339形式をdate型にするやり方がわからない。下記だとエラー
                    // 'post_time'  => $videos[$i]['snippet']['publishedAt'],
                    'post_time'  => $today,
                    'player_id'  => $player_id,
                    'created_at' => $today,
                    'updated_at' => $today,
                ];
            }
        }
        return $value;
    }
}
