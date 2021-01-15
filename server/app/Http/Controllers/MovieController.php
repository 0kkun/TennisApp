<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Top\TopServiceInterface;
use Exception;
use App\Repositories\Contracts\YoutubeVideosRepository;
use App\Repositories\Contracts\FavoritePlayersRepository;


class MovieController extends Controller
{
    private $youtube_video_repository;
    private $favorite_player_repository;

    /**
     * リポジトリをDI
     * 
     */
    public function __construct(
        YoutubeVideosRepository $youtube_video_repository,
        FavoritePlayersRepository $favorite_player_repository
    )
    {
        $this->youtube_video_repository = $youtube_video_repository;
        $this->favorite_player_repository = $favorite_player_repository;
    }


    /**
     * 動画取得用API
     *
     * @return void
     */
    public function fetchMovies(Request $request)
    {
        try {
            $user_id = $request->input('user_id');
            $is_paginate = false;
            $max_movie_num = 12;
    
            if ( $this->hasFavoritePlayer($user_id) ) {
                // お気に入り選手のidを取得
                $favorite_player_ids = $this->favorite_player_repository->getFavoritePlayers($user_id)->pluck('player_id')->toArray();

                // idを使って動画を取得
                $response = $this->youtube_video_repository->getVideosByPlayerIds($max_movie_num, $favorite_player_ids, $is_paginate);
            } else {
                $response = $this->youtube_video_repository->getAll($max_movie_num, $is_paginate);
            }

            return request()->json(200, $response);

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
