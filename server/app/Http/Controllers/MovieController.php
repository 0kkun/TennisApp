<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\YoutubeVideosRepository;
use App\Repositories\Contracts\FavoritePlayersRepository;
use App\Repositories\Contracts\BrandYoutubeVideosRepository;
use App\Repositories\Contracts\FavoriteBrandsRepository;
use Illuminate\Http\JsonResponse;
use App\Services\Api\ApiServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MovieController extends Controller
{
    private $youtube_video_repository;
    private $favorite_player_repository;
    private $brand_youtube_video_repository;
    private $favorite_brand_repository;
    private $api_service;

    // レスポンスのフォーマット
    protected $response;
    protected $result_status;

    const MAX_MOVIE_NUM = 6;

    /**
     * Constructor
     *
     * @param YoutubeVideosRepository $youtube_video_repository
     * @param FavoritePlayersRepository $favorite_player_repository
     * @param BrandYoutubeVideosRepository $brand_youtube_video_repository
     * @param FavoriteBrandsRepository $favorite_brand_repository
     * @param ApiServiceInterface $api_service
     */
    public function __construct(
        YoutubeVideosRepository $youtube_video_repository,
        FavoritePlayersRepository $favorite_player_repository,
        BrandYoutubeVideosRepository $brand_youtube_video_repository,
        FavoriteBrandsRepository $favorite_brand_repository,
        ApiServiceInterface $api_service
    )
    {
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
            $start = microtime(true);
            Log::info("[START] " . __FUNCTION__ );

            // リクエストの中身をチェック
            $is_varidation_error = $this->checkValidationError(__FUNCTION__, $request->all());
            $status = $this->getStatusCode($is_varidation_error);

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
                    // スマホ対応させる為https削除
                    $youtube_videos = $this->deleteHttpsFromUrl($youtube_videos);
                    $this->response = ['status' => $status, 'data' => $youtube_videos];

                } else {
                    $youtube_videos = $this->youtube_video_repository->getAll(self::MAX_MOVIE_NUM, $is_paginate);
                    // スマホ対応させる為https削除
                    $youtube_videos = $this->deleteHttpsFromUrl($youtube_videos);
                    $this->response = ['status' => $status, 'data' => $youtube_videos];
                }
            } else {
                $this->response = ['status' => $status, 'data' => ''];
            }

            $end = microtime(true);
            $time = $this->api_service->calcTime($start, $end);
            Log::info("[ END ] " . __FUNCTION__ . ", STATUS:" . $status . ", 処理時間:" . $time . "秒");
            return response()->json($this->response);

        } catch (\Exception $e) {
            Log::info("[Exception]" . __FUNCTION__ . $e->getMessage());
            $this->respose = $this->api_service->makeErrorResponse($e);
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
            $start = microtime(true);
            Log::info("[START] " . __FUNCTION__ );

            // リクエストの中身をチェック
            $is_varidation_error = $this->checkValidationError(__FUNCTION__, $request->all());
            $status = $this->getStatusCode($is_varidation_error);

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
                    // スマホ対応させる為https削除
                    $youtube_videos = $this->deleteHttpsFromUrl($youtube_videos);
                    $this->response = ['status' => $status, 'data' => $youtube_videos];

                // お気に入りが無い場合
                } else {
                    $youtube_videos = $this->brand_youtube_video_repository->getAll(self::MAX_MOVIE_NUM, $is_paginate);
                    // スマホ対応させる為https削除
                    $youtube_videos = $this->deleteHttpsFromUrl($youtube_videos);
                    $this->response = ['status' => $status, 'data' => $youtube_videos];
                }
            } else {
                $this->response = ['status' => $status, 'data' => ''];
            }

            $end = microtime(true);
            $time = $this->api_service->calcTime($start, $end);
            Log::info("[ END ] " . __FUNCTION__ . ", STATUS:" . $status . ", 処理時間:" . $time . "秒");

        } catch (\Exception $e) {
            Log::info("[Exception]" . __FUNCTION__ . $e->getMessage());
            $this->respose = $this->api_service->makeErrorResponse($e);
        }

        return response()->json($this->response);
    }


    /**
     * 動画のURLから"https/:"を削除する
     *
     * @param Collection $youtube_videos
     * @return Collection
     */
    private function deleteHttpsFromUrl(Collection $youtube_videos): Collection
    {
        $results = collect();
        foreach ($youtube_videos as $video) {
            $video->url = substr($video->url, 6, 1000);
            $results->push($video);
        }
        return $results;
    }


    /**
     * バリデーションエラーか判定する
     *
     * @param string $func_name
     * @param array $check_keys
     * @return boolean
     */
    private function checkValidationError(string $func_name, array $check_keys): bool
    {
        $func_and_keys_pattern = [
            'fetchPlayerMovies' => [
                'user_id' => 'required|integer'
            ],
            'fetchBrandMovies' => [
                'user_id' => 'required|integer'
            ],
        ];
        $validator = Validator::make($check_keys, $func_and_keys_pattern[$func_name]);

        $is_validation_error = !empty($validator->errors()->messages());

        return $is_validation_error;
    }


    /**
     * バリデーションチェックの結果に基づくステータスコードを取得
     *
     * @param boolean $is_validation_error
     * @return integer
     */
    private function getStatusCode(bool $is_validation_error): int
    {
        return $is_validation_error ? $this->result_status['bad_request'] : $this->result_status['success'];
    }
}
