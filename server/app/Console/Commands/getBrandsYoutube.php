<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Contracts\BrandsRepository;
use App\Repositories\Contracts\BrandYoutubeVideosRepository;
use Carbon\Carbon;
use Google_Client;
use Google_Service_YouTube;
use Google_Service_Exception;
use Google_Exception;
use App\Modules\BatchLogger;
use Exception;

class getBrandsYoutube extends Command
{

    protected $signature = 'command:getBrandsYoutube {--sync : 同期処理}';
    protected $description = 'ブランドのyoutube動画を取得する';
    const MAX_COUNT = 5;
    private $brands_repository;
    private $brand_youtube_videos_repository;


    /**
     * リポジトリのコンストラクタ
     *
     * @param BrandsRepository $brands_repository
     * @param BrandYoutubeVideosRepository $brand_youtube_videos_repository
     */
    public function __construct(
        BrandsRepository $brands_repository,
        BrandYoutubeVideosRepository $brand_youtube_videos_repository
    )
    {
        parent::__construct();
        $this->brands_repository = $brands_repository;
        $this->brand_youtube_videos_repository = $brand_youtube_videos_repository;
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info( "実行開始" );
        $this->logger = new BatchLogger( 'getBrandsYoutube' );

        try {
            $client = new Google_Client();

            // $client->setDeveloperKey(config('app.google_api_key1'));
            // $client->setDeveloperKey(config('app.google_api_key2')); // クォータが尽きた時用の予備
            // $client->setDeveloperKey(config('app.google_api_key3')); // クォータが尽きた時用の予備
            $client->setDeveloperKey( config('app.google_api_key4') ); // クォータが尽きた時用の予備

            $youtube = new Google_Service_YouTube( $client );

            // 日付はRFC3339形式にしないと読み込め無いのでフォーマット
            $one_week_ago = strtotime( '-30 day' );
            $search_duration = date(DATE_RFC3339, $one_week_ago);

            $brands = $this->brands_repository->getAll()->toArray();

            for ( $i=0; $i<count($brands); $i++ ) {
                $check_word = $brands[$i]['name_en'];
                $keyword = "インプレ" . " " . $brands[$i]['name_en'];
                $brand_id = $brands[$i]['id'];
                $this->info( $check_word . 'の動画取得開始' );
                $videos = $this->getYoutube( $youtube, $search_duration, $keyword );
                sleep(0.5);

                $video_data = $this->makeInsertValue( $videos, $brand_id, $check_word );

                if( !empty($video_data) ){
                    $this->brand_youtube_videos_repository->bulkInsertOrUpdate( $video_data );
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
        $this->info( "実行終了" );
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
     * @param integer $brand_id
     * @param string $check_word
     * @return array
     */
    public function makeInsertValue( array $videos, int $brand_id, string $check_word ): array
    {
        $today = Carbon::now();
        $count = count($videos);
        $value = array();

        for ( $i=0; $i<$count; $i++ ) {
            if ( stripos($videos[$i]['snippet']['title'], $check_word) !== false ) {
                $value[$i] = [
                    'title'      => $videos[$i]['snippet']['title'],
                    'url'        => 'https://www.youtube.com/embed/' . $videos[$i]['id']['videoId'],
                    // FIXME: RFC3339形式をdate型にするやり方がわからない。下記だとエラー
                    // 'post_time'  => $videos[$i]['snippet']['publishedAt'],
                    'post_time'  => $today,
                    'brand_id'   => $brand_id,
                    'created_at' => $today,
                    'updated_at' => $today,
                ];
            }
        }
        return $value;
    }
}
