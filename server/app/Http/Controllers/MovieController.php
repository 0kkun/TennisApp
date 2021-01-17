<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Repositories\Contracts\YoutubeVideosRepository;
use App\Repositories\Contracts\FavoritePlayersRepository;
use App\Repositories\Contracts\BrandYoutubeVideosRepository;
use App\Repositories\Contracts\FavoriteBrandsRepository;


class MovieController extends Controller
{
    private $youtube_video_repository;
    private $favorite_player_repository;
    private $brand_youtube_video_repository;
    private $favorite_brand_repository;

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
        FavoriteBrandsRepository $favorite_brand_repository
    )
    {
        $this->youtube_video_repository = $youtube_video_repository;
        $this->favorite_player_repository = $favorite_player_repository;
        $this->brand_youtube_video_repository = $brand_youtube_video_repository;
        $this->favorite_brand_repository = $favorite_brand_repository;
    }


    /**
     * [API] お気に入りに登録されたプレイヤーに基づいてYoutube取得
     *
     * @param Request $request
     * @return Json|Exception
     */
    public function fetchPlayerMovies(Request $request)
    {
        try {
            $user_id = $request->input('user_id');
            $is_paginate = false;

            if ( $this->hasFavoritePlayer($user_id) ) {
                // お気に入り選手のidを取得
                $favorite_player_ids = $this->favorite_player_repository->getFavoritePlayers($user_id)
                    ->pluck('player_id')
                    ->toArray();

                // idを使って動画を取得
                $response = $this->youtube_video_repository
                    ->getVideosByPlayerIds(self::MAX_MOVIE_NUM, $favorite_player_ids, $is_paginate);

                return request()->json(200, $response);

            } else {
                $response = $this->youtube_video_repository->getAll(self::MAX_MOVIE_NUM, $is_paginate);
                return request()->json(200, $response);
            }

        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }


    /**
     * [API] お気に入りに登録されたプレイヤーに基づいてYoutube取得
     *
     * @param Request $request
     * @return Json|Exception
     */
    public function fetchBrandMovies(Request $request)
    {
        try {
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
                $response = $this->brand_youtube_video_repository
                    ->getVideosByBrandIds(self::MAX_MOVIE_NUM, $favorite_brand_ids, $is_paginate);
                return request()->json(200, $response);

            // お気に入りが無い場合
            } else {
                $response = $this->brand_youtube_video_repository->getAll(self::MAX_MOVIE_NUM, $is_paginate);
                return request()->json(200, $response);
            }
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * ユーザーがお気に入り選手を登録しているかどうかチェック
     *
     * @return boolean
     */
    private function hasFavoritePlayer(int $user_id): bool
    {
        $count = count($this->favorite_player_repository->getAll($user_id));
        return ($count > 0) ? true : false;
    }
}
