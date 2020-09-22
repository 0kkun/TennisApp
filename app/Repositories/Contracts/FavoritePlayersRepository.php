<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface FavoritePlayersRepository
{
    /**
     * 全レコードを取得
     *
     * @return Collection
     */  
    public function getAll(): Collection;


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
}