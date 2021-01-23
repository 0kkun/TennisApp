<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Repositories\Contracts\YoutubeVideosRepository;
use App\Repositories\Contracts\FavoritePlayersRepository;
use App\Repositories\Contracts\BrandYoutubeVideosRepository;
use App\Repositories\Contracts\FavoriteBrandsRepository;
use Illuminate\Http\JsonResponse;
use App\Services\Api\ApiServiceInterface;
use App\Modules\BatchLogger;

class MovieController extends Controller
{
    private $youtube_video_repository;
    private $favorite_player_repository;
    private $brand_youtube_video_repository;
    private $favorite_brand_repository;
    private $api_service;
    private $logger;

    // レスポンスのフォーマット
    protected $response;
    protected $result_status;

    const MAX_MOVIE_NUM = 6;

    /**
     * リポジトリをDI
     *
     * @param YoutubeVideosRepository $youtube_video_repository
     * @param FavoritePlayersRepository $favorite_player_repository
     */
    public function __construct(
        YoutubeVideosRepository $youtube_video_repository,
        FavoritePlayersRepository $favorite_player_repository,
        BrandYoutubeVideosRepository $brand_youtube_video_repository,
        FavoriteBrandsRepository $favorite_brand_repository,
        ApiServiceInterface $api_service
    )
    {
        $this->logger = new BatchLogger('FavoritePlayerController');
        $this->response = config('api_template.response_format');
        $this->result_status = config('api_template.result_status');
        $this->youtube_video_repository = $youtube_video_repository;
        $this->favorite_player_repository = $favorite_player_repository;
        $this->brand_youtube_video_repository = $brand_youtube_video_repository;
        $this->favorite_brand_repository = $favorite_brand_repository;
        $this->api_service = $api_service;
    }


    /**
     * [API] お気に入りに登録されたプレイヤーに基づいてYoutube取得
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function fetchPlayerMovies(Request $request): JsonResponse
    {
        try {
            // リクエストの中身をチェック
            $expected_key = ['user_id'];
            $status = $this->api_service->checkArgs($request, $expected_key);

            if ($status === $this->result_status['success']) {

                $user_id = $request->input('user_id');
                $is_paginate = false;

                // お気に入り選手のidを取得
                $favorite_player_ids = $this->favorite_player_repository->getFavoritePlayers($user_id)
                    ->pluck('player_id')
                    ->toArray();

                // お気に入り選手がある場合
                if ( !empty($favorite_player_ids) ) {
                    // idを使って動画を取得
                    $youtube_videos = $this->youtube_video_repository
                        ->getVideosByPlayerIds(self::MAX_MOVIE_NUM, $favorite_player_ids, $is_paginate);

                    $this->response = ['status' => $status, 'data' => $youtube_videos];

                } else {
                    $youtube_videos = $this->youtube_video_repository->getAll(self::MAX_MOVIE_NUM, $is_paginate);
                    $this->response = ['status' => $status, 'data' => $youtube_videos];
                }
            }

            $this->logger->write('status code :' . $status, 'info');
            $this->logger->success();

            return response()->json($this->response);

        } catch (Exception $e) {
            $this->logger->exception($e);
            $status = $this->result_status['server_error'];
            $error_info = $this->api_service->makeErrorInfo($e);
            $this->response = ['status' => $status,'data' => $error_info];

            return response()->json($this->response);
        }
    }


    /**
     * [API] お気に入りに登録されたプレイヤーに基づいてYoutube取得
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function fetchBrandMovies(Request $request): JsonResponse
    {
        try {
            // リクエストの中身をチェック
            $expected_key = ['user_id'];
            $status = $this->api_service->checkArgs($request, $expected_key);

            if ($status === $this->result_status['success']) {

                $user_id = $request->input('user_id');
                $is_paginate = false;

                // お気に入りブランドのidを取得
                $favorite_brand_ids = $this->favorite_brand_repository
                    ->fetchFavoriteBrands($user_id)
                    ->pluck('brand_id')
                    ->toArray();

                // お気に入りがある場合
                if ( !empty($favorite_brand_ids) ) {
                    // idを使って動画を取得
                    $youtube_videos = $this->brand_youtube_video_repository
                        ->getVideosByBrandIds(self::MAX_MOVIE_NUM, $favorite_brand_ids, $is_paginate);

                    $this->response = ['status' => $status, 'data' => $youtube_videos];

                // お気に入りが無い場合
                } else {
                    $youtube_videos = $this->brand_youtube_video_repository->getAll(self::MAX_MOVIE_NUM, $is_paginate);
                    $this->response = ['status' => $status, 'data' => $youtube_videos];
                }
            }

            $this->logger->write('status code :' . $status, 'info');
            $this->logger->success();

            return response()->json($this->response);

        } catch (Exception $e) {
            $this->logger->exception($e);
            $status = $this->result_status['server_error'];
            $error_info = $this->api_service->makeErrorInfo($e);
            $this->response = ['status' => $status,'data' => $error_info];

            return response()->json($this->response);
        }
    }
}
