<?php

namespace App\Repositories\Eloquents;

use App\Models\FavoritePlayer;
use App\Repositories\Contracts\FavoritePlayersRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class EloquentFavoritePlayersRepository implements FavoritePlayersRepository
{
    protected $favorite_players;


    /**
    * @param object $favorite_players
    */
    public function __construct(
        FavoritePlayer $favorite_players
    )
    {
        $this->favorite_players = $favorite_players;
    }


    /**
     * ログインユーザーの登録したお気に入り選手レコードを取得
     *
     * @return Collection
     */
    public function getAll(?int $user_id=null): Collection
    {
        if( empty($user_id) ) {
            $user_id = Auth::user()->id;
        }
        return $this->favorite_players
                    ->where('user_id', '=', $user_id)
                    ->get();
    }


    /**
     * バルクインサート処理
     *
     * @param  Collection|FavoritePlayers|array $data
     * @return void
     */
    public function bulkInsertOrUpdate($data): void
    {
        $this->favorite_players->bulkInsertOrUpdate($data);
    }


    /**
     * お気に入り選手削除メソッド
     *
     * @param integer $favorite_player_id
     * @return void
     */
    public function deleteRecord(array $data): void
    {
        if ( !isset($data['user_id']) ) {
            $data['user_id'] = Auth::user()->id;
        }
        $this->favorite_players
            ->where('user_id', $data['user_id'])
            ->where('player_id', $data['player_id'])
            ->delete();
    }


    /**
     * ログインユーザーが持っているお気に入り選手の名前・出身を取得する
     *
     * @param integer|null $user_id
     * @return Collection
     */
    public function getFavoritePlayers(?int $user_id=null): Collection
    {
        if ( empty($user_id) ) $user_id = Auth::user()->id;

        return $this->favorite_players
                    ->where('user_id', $user_id)
                    ->with('players')
                    ->join('players', 'favorite_players.player_id', 'players.id')
                    ->get();
    }
}