<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Contracts\FavoritePlayersRepository;
use App\Repositories\Contracts\PlayersNewsArticleRepository;
use App\Repositories\Contracts\BrandNewsArticlesRepository;
use App\Repositories\Contracts\FavoriteBrandsRepository;
use Illuminate\Http\JsonResponse;
use App\Services\Api\ApiServiceInterface;
use App\Modules\BatchLogger;

class NewsController extends Controller
{
    private $favorite_player_repository;
    private $players_news_article_repository;
    private $favorite_brand_repository;
    private $brand_news_article_repository;
    private $api_service;
    private $logger;

    // レスポンスのフォーマット
    protected $response;
    protected $result_status;

    const MAX_ARTICLE_NUM = 9;

    /**
     * リポジトリをDI
     *
     * @param FavoritePlayersRepository $favorite_player_repository
     * @param PlayersNewsArticleRepository $players_news_article_repository
     * @param FavoriteBrandsRepository $favorite_brand_repository
     * @param BrandNewsArticlesRepository $brand_news_article_repository
     * @param ApiServiceInterface $api_service
     */
    public function __construct(
        FavoritePlayersRepository $favorite_player_repository,
        PlayersNewsArticleRepository $players_news_article_repository,
        FavoriteBrandsRepository $favorite_brand_repository,
        BrandNewsArticlesRepository $brand_news_article_repository,
        ApiServiceInterface $api_service
    )
    {
        $this->logger = new BatchLogger(__CLASS__);
        $this->response = config('api_template.response_format');
        $this->result_status = config('api_template.result_status');
        $this->favorite_player_repository = $favorite_player_repository;
        $this->players_news_article_repository = $players_news_article_repository;
        $this->favorite_brand_repository = $favorite_brand_repository;
        $this->brand_news_article_repository = $brand_news_article_repository;
        $this->api_service = $api_service;
    }


    /**
     * 新しいデザインのニューストップ画面
     */
    public function top()
    {
        if (Auth::check()) {
            $user_id = Auth::id();
            return view('news.top', compact('user_id'));
        } else {
            return view('top.index');
        }
    }


    /**
     * [API] お気に入りに基づいたテニスニュース記事を取得する
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function fetchPlayersNews(Request $request): JsonResponse
    {
        try {
            // リクエストの中身をチェック
            $expected_key = ['user_id'];
            $status = $this->api_service->checkArgs($request, $expected_key);

            if ($status === $this->result_status['success']) {

                $user_id = $request->input('user_id');
                $is_paginate = false;
                
                // ログインユーザーのお気に入り選手を取得する
                $favorite_players = $this->favorite_player_repository
                    ->getFavoritePlayers($user_id)
                    ->toArray();

                // ファーストネームだけにする
                $player_names = $this->getFirstName( $favorite_players );

                // お気に入りが無い場合は全件取得
                if ( empty($favorite_players) ) {
                    $news_articles = $this->players_news_article_repository
                        ->fetchArticles(self::MAX_ARTICLE_NUM, $is_paginate);

                    $this->response = ['status' => $status, 'data' => $news_articles];

                // お気に入りがある場合は絞る
                } else {
                    // お気に入り選手の名前で記事を検索し取得
                    $news_articles = $this->players_news_article_repository
                        ->fetchArticlesByPlayerNames($player_names, self::MAX_ARTICLE_NUM, $is_paginate);

                    $this->response = ['status' => $status, 'data' => $news_articles];
                }
            } else {
                $this->response = ['status' => $status, 'data' => ''];
            }

            $this->logger->write('status code :' . $status, 'info');
            $this->logger->success();

            return response()->json($this->response);

        } catch ( Exception $e ) {
            $this->logger->exception($e);
            $status = $this->result_status['server_error'];
            $error_info = $this->api_service->makeErrorInfo($e);
            $this->response = ['status' => $status,'data' => $error_info];

            return response()->json($this->response);
        }
    }


    /**
     * [API] お気に入りブランドに基づいたニュースを取得する
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function fetchBrandsNews(Request $request): JsonResponse
    {
        try {
            // リクエストの中身をチェック
            $expected_key = ['user_id'];
            $status = $this->api_service->checkArgs($request, $expected_key);

            if ($status === $this->result_status['success']) {

                $user_id = $request->input('user_id');
                $is_paginate = false;
                
                $favorite_brand_names = $this->favorite_brand_repository
                    ->fetchFavoriteBrands($user_id)
                    ->pluck('name_en')
                    ->toArray();
    
                // お気に入りが無い場合は全件取得
                if ( empty($favorite_brand_names) ) {
                    $brand_news = $this->brand_news_article_repository
                        ->fetchArticles(self::MAX_ARTICLE_NUM, $is_paginate);

                    $this->response = ['status' => $status, 'data' => $brand_news];

                // お気に入りがある場合は絞る
                } else {
                    $brand_news = $this->brand_news_article_repository
                        ->fetchArticlesByBrandNames($favorite_brand_names, self::MAX_ARTICLE_NUM, $is_paginate);

                    $this->response = ['status' => $status, 'data' => $brand_news];
                }
            } else {
                $this->response = ['status' => $status, 'data' => ''];
            }

            $this->logger->write('status code :' . $status, 'info');
            $this->logger->success();

            return response()->json($this->response);

        } catch ( Exception $e ) {
            $this->logger->exception($e);
            $status = $this->result_status['server_error'];
            $error_info = $this->api_service->makeErrorInfo($e);
            $this->response = ['status' => $status,'data' => $error_info];

            return response()->json($this->response);
        }
    }


    /**
     * ファーストネームだけにして返す
     *
     * @param array $players
     * @return array
     */
    private function getFirstName( array $players ):array
    {
        $names = array();
        $kanji_pattern = "/^[一-龠]+$/u";

        foreach ( $players as $index => $player) {
            $frequency_count = substr_count($player['name_jp'], '・');

            // 出身が日本かつ漢字なら、最初の2文字を抜き出す
            if ( preg_match( $kanji_pattern, $player['name_jp']) && $player['country'] === '日本' ) {
                $names[$index] = mb_substr($player['name_jp'], 0, 2);

            // "・"が名前に入っていない場合はそのままいれる
            } else if ( $frequency_count === 0 ) {
                $names[$index] = $player['name_jp'];

            // "・"がある場合は最後の文字列だけいれる
            } else {
                $divided_name = explode("・",$player['name_jp']);
                $names[$index] = $divided_name[$frequency_count];
            }
        }
        return $names;
    }
}
