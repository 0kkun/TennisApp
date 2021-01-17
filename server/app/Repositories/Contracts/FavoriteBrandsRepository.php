<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface FavoriteBrandsRepository
{
    /**
     * ログインユーザーが持っているお気に入りブランドの全レコードを取得
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
     * お気に入りブランド削除メソッド
     *
     * @param array $data
     * @return void
     */
    public function deleteRecord(array $data): void;


    /**
     * ログインユーザーが持っているお気に入りブランドの名前・製造国を取得する
     *
     * @param integer|null $user_id
     * @return Collection
     */
    public function fetchFavoriteBrands(?int $user_id=null): Collection;
}