<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface FavoritePlayersRepository
{
    /**
     * ログインユーザーが持っているお気に入り選手の全レコードを取得
     *
     * @return Collection
     */  
    public function getAll(?int $user_id=null): Collection;


    /**
     * バルクインサート
     * テーブルに保存・更新する
     * 
     * @param array $data
     * @return void
     */
    public function bulkInsertOrUpdate($data): void;


    /**
     * お気に入り選手削除メソッド
     *
     * @param integer $favorite_player_id
     * @return void
     */
    public function deleteRecord(int $favorite_player_id): void;


    /**
     * ログインユーザーが持っているお気に入り選手の名前・出身を取得する
     *
     * @param integer|null $user_id
     * @return Collection
     */
    public function getFavoritePlayers(?int $user_id=null): Collection;
}