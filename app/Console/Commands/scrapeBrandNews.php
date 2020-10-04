<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Weidner\Goutte\GoutteFacade;
use App\Modules\BatchLogger;
use App\Repositories\Contracts\BrandNewsArticlesRepository;
use Exception;
use Carbon\Carbon;

class scrapeBrandNews extends Command
{
    protected $signature = 'command:scrapeBrandNews {--sync : 同期処理}';

    protected $description = 'ブランドのニュースをスクレイピングで取得し保存するコマンド';

    private $brand_news_articles_repository;


    /**
     * リポジトリのコンストラクタ
     *
     * @param BrandNewsArticlesRepository $brand_news_articles_repository
     */
    public function __construct(
        BrandNewsArticlesRepository $brand_news_articles_repository
    )
    {
        parent::__construct();
        $this->brand_news_articles_repository = $brand_news_articles_repository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $is_sync = $this->option('sync');

        $this->info("実行開始");
        $this->logger = new BatchLogger( 'scrapeBrandNews' );

        try {
            $title      = array();
            $url        = array();
            $post_time  = array();
            $brand_name = array();

            // スクレイピングするサイト
            $yonex_url = 'https://www.yonex.co.jp/tennis/news/products/';
            $wilson_url = 'https://www.wilson.co.jp/news/tennis_news/';
            $prince_url = 'https://princetennis.jp/archives/category/tennis';
            $srixon_url = 'https://sports.dunlop.co.jp/tennis/updates/products/';
            $tennis_eval_url_1 = 'https://tenniseval.com/';
            $tennis_eval_url_2 = 'https://tenniseval.com/page/2/';
            $tennis_eval_url_3 = 'https://tenniseval.com/page/3/';

            // スクレイピング実行
            $this->scrapeYonexSite( $yonex_url, $title, $url, $post_time, $brand_name );
            $this->scrapeWilsonSite( $wilson_url, $title, $url, $post_time, $brand_name );
            $this->scrapePrinceSite( $prince_url, $title, $url, $post_time, $brand_name );
            $this->scrapeSrixonSite( $srixon_url, $title, $url, $post_time, $brand_name );
            $this->scrapeTennisEvalSite( $tennis_eval_url_1, $title, $url, $post_time, $brand_name );
            $this->scrapeTennisEvalSite( $tennis_eval_url_2, $title, $url, $post_time, $brand_name );
            $brand_news_articles = $this->scrapeTennisEvalSite( $tennis_eval_url_3, $title, $url, $post_time, $brand_name );

            // バルクインサートで保存
            if ( !empty($brand_news_articles) ) {
                $this->brand_news_articles_repository->bulkInsertOrUpdate( $brand_news_articles );
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
     * stringのmonthをintに変換する
     *
     * @param string $month
     * @return integer
     */
    private function changeMonthToInt( string $month ): int
    {
        if ( $month=="Jan" ) {
            $month = (int) 1;
        } else if ( $month=="Feb" ) {
            $month = (int) 2;
        } else if ( $month=="Mar" ) {
            $month = (int) 3;
        } else if ( $month=="Apr" ) {
            $month = (int) 4;
        } else if ( $month=="May" ) {
            $month = (int) 5;
        } else if ( $month=="Jun" ) {
            $month = (int) 6;
        } else if ( $month=="Jul" ) {
            $month = (int) 7;
        } else if ( $month=="Aug" ) {
            $month = (int) 8;
        } else if ( $month=="Sep" ) {
            $month = (int) 9;
        } else if ( $month=="Oct" ) {
            $month = (int) 10;
        } else if ( $month=="Nov" ) {
            $month = (int) 11;
        } else {
            $month = (int) 12;
        }

        return $month;
    }


    /**
     * Undocumented function
     *
     * @param array $title
     * @param array $url
     * @param array $post_time
     * @param array $brand_name
     * @return array
     */
    private function makeInsertValue( array $title, array $url, array $post_time, array $brand_name): array
    {
        $count = count( $title );
        $today = Carbon::now();

        for ( $i=0; $i<$count; $i++ ) {
            $value[$i] = [
                'title'      => $title[$i],
                'url'        => $url[$i],
                'post_time'  => $post_time[$i],
                'brand_name' => $brand_name[$i],
                'created_at' => $today,
                'updated_at' => $today
            ];
        }
        return $value;
    }


    /**
     * 余分なテキストを省いて日付情報に加工する
     *
     * @param string $scraped_date
     * @return Carbon
     */
    private function excludeTextFromDataToDate( string $scraped_date ): Carbon
    {
        $pattern = '/([0-9]{4})(\/|-|年|.)([0-9]{1,2})(\/|-|月|.)([0-9]{1,2})/';
        $scraped_date = str_replace( '.', '/', $scraped_date );
        preg_match($pattern, $scraped_date, $date);
        $carbonated_date = Carbon::parse($date[0]);

        return $carbonated_date;
    }


    /**
     * タイトルテキストからブランド名を判別する
     *
     * @param string $title
     * @return string
     */
    private function judgeBrandName( string $title ): string
    {
        $brand = '';

        if ( strpos( $title , 'ヨネックス' ) !== false ) {
            $brand = 'Yonex';
        } else if ( strpos( $title , 'ウィルソン' ) !== false ) {
            $brand = 'Wilson';
        } else if ( strpos( $title , 'バボラ' ) !== false ) {
            $brand = 'Babolat';
        } else if ( strpos( $title , 'ヘッド' ) !== false ) {
            $brand = 'HEAD';
        } else if ( strpos( $title , 'プリンス' ) !== false ) {
            $brand = 'prince';
        } else if ( strpos( $title , 'ダンロップ' ) !== false ) {
            $brand = 'SRIXON';
        } else if ( strpos( $title , 'スリクソン' ) !== false ) {
            $brand = 'SRIXON';
        } else {
            $brand =  'Other';
        }
        return $brand;
    }


    /**
     * Yonexサイトをスクレイピングしデータを作成する
     *
     * @param string $site_url
     * @param array $title
     * @param array $url
     * @param array $post_time
     * @param array $brand_name
     * @return array
     */
    private function scrapeYonexSite( string $site_url, array &$title, array &$url, array &$post_time, array &$brand_name ):array
    {
        $year = Carbon::now()->year;
        $goutte = GoutteFacade::request('GET', $site_url);
        sleep(1);

        $goutte->filter('ul.newslist')->each(function ($node) use (&$title, &$url, &$post_time, &$brand_name, &$year) {
            if ( $node->count() > 0 ) {
                $count =  $node->filter('li')->count();
                for ( $i=0; $i<$count; $i++ ) {
                    array_push( $title, $node->filter('.blogtitle')->eq($i)->text() );
                    array_push( $url, 'https://www.yonex.co.jp' . $node->filter('a')->attr('href') );

                    // スクレイピングしたmonthは"Sep"などのようになっているのでintに変換する
                    $month_string = $node->filter('.date .month')->eq($i)->text();
                    $month_int = $this->changeMonthToInt( $month_string );
                    $day = $node->filter('.date .day')->eq($i)->text();
                    $date =  Carbon::create( $year, $month_int, $day, 0, 0 );
                    array_push( $post_time, $date );

                    array_push( $brand_name, 'Yonex' );
                }
            } else {
                $this->info("スクレイピング実行できませんでした。");
            }
        });
        $brand_news_data = $this->makeInsertValue( $title, $url, $post_time, $brand_name);

        return $brand_news_data;
    }


    /**
     * ウィルソンサイトをスクレイピングしデータを作成する
     *
     * @param string $site_url
     * @param array $title
     * @param array $url
     * @param array $post_time
     * @param array $brand_name
     * @return array
     */
    private function scrapeWilsonSite( string $site_url, array &$title, array &$url, array &$post_time, array &$brand_name ):array
    {
        $goutte  = GoutteFacade::request('GET', $site_url);
        sleep(1);

        $goutte->filter('ul.news-list')->each(function ($node) use (&$title, &$url, &$post_time, &$brand_name) {
            if ( $node->count() > 0 ) {
                $count =  $node->filter('li')->count();
                for ( $i=0; $i<$count; $i++ ) {
                    array_push( $title, $node->filter('dt')->eq($i)->text() );
                    array_push( $url, $node->filter('a')->eq($i)->attr('href') );

                    $scraped_date = $node->filter('.date')->eq($i)->text();
                    $date = $this->excludeTextFromDataToDate( $scraped_date );
                    array_push( $post_time, $date );
                    array_push( $brand_name, 'Wilson' );
                }
            } else {
                $this->info("スクレイピング実行できませんでした。");
            }
        });

        $brand_news_data = $this->makeInsertValue( $title, $url, $post_time, $brand_name);

        return $brand_news_data;
    }


    /**
     * プリンスサイトをスクレイピングしデータを作成する
     *
     * @param string $site_url
     * @param array $title
     * @param array $url
     * @param array $post_time
     * @param array $brand_name
     * @return array
     */
    private function scrapePrinceSite( string $site_url, array &$title, array &$url, array &$post_time, array &$brand_name ):array
    {
        $goutte  = GoutteFacade::request('GET', $site_url);
        sleep(1);

        $goutte->filter('ul.entries-list')->each(function ($node) use (&$title, &$url, &$post_time, &$brand_name) {
            if ( $node->count() > 0 ) {
                for ( $i=0; $i<40; $i++ ) {
                    $scraped_text = $node->filter('.entries-list__title')->eq($i)->text();
                    // 不必要なニュースが多いので'発売'ワードで絞り込みする
                    if ( strpos( $scraped_text , '発売' ) !==false) {
                        array_push( $title, $node->filter('.entries-list__title')->eq($i)->text() );
                        array_push( $url, $node->filter('.entries-list__title a')->eq($i)->attr('href') );
                        $scraped_date = $node->filter('.entries-list__date')->eq($i)->text();
                        $date = $this->excludeTextFromDataToDate( $scraped_date );
                        array_push( $post_time, $date );
                        array_push( $brand_name, 'prince' );
                    }
                }
            } else {
                $this->info("スクレイピング実行できませんでした。");
            }
        });
        $brand_news_data = $this->makeInsertValue( $title, $url, $post_time, $brand_name);

        return $brand_news_data;
    }


    /**
     * スリクソンサイトをスクレイピングしデータを作成する
     *
     * @param string $site_url
     * @param array $title
     * @param array $url
     * @param array $post_time
     * @param array $brand_name
     * @return array
     */
    private function scrapeSrixonSite( string $site_url, array &$title, array &$url, array &$post_time, array &$brand_name ):array
    {
        $year = Carbon::now()->year;
        $goutte  = GoutteFacade::request('GET', $site_url . $year . '/');
        sleep(1);

        $goutte->filter('ul.mod-newsList')->each(function ($node) use (&$title, &$url, &$post_time, &$brand_name) {
            if ( $node->count() > 0 ) {
                $count =  $node->filter('li')->count();
                for ( $i=0; $i<$count; $i++ ) {
                    array_push( $title, $node->filter('.mod-newsList-title')->eq($i)->text() );
                    // https ~がある場合と無い場合があるので条件分け
                    $url_temp = $node->filter('a')->eq($i)->attr('href');
                    if ( strpos( $url_temp , 'http' ) !==false ) {
                        array_push( $url, $url_temp );
                    } else {
                        array_push( $url, 'https://sports.dunlop.co.jp' . $url_temp );
                    }
                    $scraped_date = $node->filter('.mod-newsList-date')->eq($i)->text();
                    $date = $this->excludeTextFromDataToDate( $scraped_date );
                    array_push( $post_time, $date );
                    array_push( $brand_name, 'SRIXON' );
                }
            } else {
                $this->info("スクレイピング実行できませんでした。");
            }
        });
        $brand_news_data = $this->makeInsertValue( $title, $url, $post_time, $brand_name);

        return $brand_news_data;
    }


    /**
     * Tennis Evalサイトをスクレイピングしデータを作成する
     *
     * @param array $tennis_eval_url
     * @param array $title
     * @param array $url
     * @param array $post_time
     * @param array $brand_name
     * @return array
     */
    private function scrapeTennisEvalSite( string $site_url, array &$title, array &$url, array &$post_time, array &$brand_name ): array
    {
        $goutte  = GoutteFacade::request('GET', $site_url);
        sleep(1);

        $goutte->filter('#list')->each(function ($node) use (&$title, &$url, &$post_time, &$brand_name) {
            if ( $node->count() > 0 ) {
                for ( $i=0; $i<6; $i++ ) {
                    $scraped_title = $node->filter('.entry-card-title')->eq($i)->text();
                    array_push( $title, $scraped_title );
                    array_push( $url, $node->filter('a')->eq($i)->attr('href') );

                    $scraped_date = $node->filter('.post-date')->eq($i)->text();
                    $date = $this->excludeTextFromDataToDate( $scraped_date );
                    array_push( $post_time, $date );

                    $brand = $this->judgeBrandName( $scraped_title );

                    array_push( $brand_name, $brand );
                }
            } else {
                $this->info("スクレイピング実行できませんでした。");
            }
        });
        $brand_news_data = $this->makeInsertValue( $title, $url, $post_time, $brand_name);

        return $brand_news_data;
    }
}
