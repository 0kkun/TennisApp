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
     * お気に入りブランド削除メソッド
     *
     * @param integer $favorite_brand_id
     * @return void
     */
    public function deleteRecord(int $favorite_brand_id): void;


    /**
     * ログインユーザーが持っているお気に入りブランドの名前・製造国を取得する
     *
     * @return Collection
     */
    public function getFavoriteBrandData(): Collection;
}