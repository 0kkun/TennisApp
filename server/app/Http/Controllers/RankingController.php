<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\RankingRepository;
use App\Repositories\Contracts\FavoritePlayersRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

// TODO: 要リファクタ
class RankingController extends Controller
{
    private $ranking_repository;
    private $favorite_player_repository;


    /**
     * リポジトリをDI
     *
     * @param RankingRepository $ranking_repository
     */
    public function __construct(
        RankingRepository $ranking_repository,
        FavoritePlayersRepository $favorite_player_repository
    )
    {
        $this->ranking_repository = $ranking_repository;
        $this->favorite_player_repository = $favorite_player_repository;
    }


    /**
     * 新しいデザインのランキングトップ画面
     */
    public function top()
    {
        if (Auth::check()) {
            $user_id = Auth::user()->id;
            return view('ranking.top', compact('user_id'));
        } else {
            return view('top.index');
        }
    }


    /**
     * [API] ランキング取得メソッド
     * 
     * @param Request $request
     * @return Json|Exception
     */
    public function fetchRankings(Request $request)
    {
        try {
            $num = $request->input('num');
            $user_id = $request->input('user_id');

            $favorite_player_ids = $this->favorite_player_repository
                ->getAll($user_id)
                ->pluck('player_id');

            $rankings = $this->ranking_repository->fetchRankings($num);

            $response = $this->makeRankingLists($rankings, $favorite_player_ids);

            return response()->json($response, 200);

        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }


    /**
     * お気に入り選手が含まれている場合はフラグを立てつつ配列に加工する
     * TODO: 他のコントローラーでも似たようなメソッドがある。サービス化したい。
     *
     * @param Collection $players
     * @param Collection $favorite_player_ids
     * @return array
     */
    private function makeRankingLists(Collection $players, Collection $favorite_player_ids): array
    {
        $player_lists = [];

        foreach ( $players as $index => $player ) {
            $player_lists[] = [
                'id'              => $player->id,
                'rank' => $player->id,
                'most_highest' => $player->most_highest,
                'name_en' => $player->name_en,
                'name_jp' => $player->name_jp,
                'age' => $player->age,
                'country' => $player->country,
                'point' => $player->point,
                'rank_change' => $player->rank_change,
                'point_change' => $player->point_change,
                'current_tour_result_en' => $player->current_tour_result_en,
                'current_tour_result_jp' => $player->current_tour_result_jp,
                'pre_tour_result_en' => $player->pre_tour_result_en,
                'pre_tour_result_jp' =>$player->pre_tour_result_jp,
                'next_point' => $player->next_point,
                'max_point' => $player->max_point,
                'is_favorited'    => false
            ];

            if ( count($favorite_player_ids) > 0 ) { 
                $player_lists[$index]['is_favorited'] = $this->isFavorite($favorite_player_ids, $player_lists[$index]['id']);
            }
        }

        return $player_lists;
    }

    /**
     * お気に入りかどうか判定する
     *
     * @param Collection $favorite_ids
     * @param integer $id
     * @return boolean
     */
    private function isFavorite(Collection $favorite_ids, int $id): bool
    {
        $is_favorited = false;
        foreach ( $favorite_ids as $favorite_id ) {
            if ( $favorite_id == $id ) $is_favorited = true;
        }
        return $is_favorited;
    }

}
