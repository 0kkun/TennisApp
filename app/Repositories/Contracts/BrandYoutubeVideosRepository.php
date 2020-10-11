<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface BrandYoutubeVideosRepository
{
    /**
     * 全レコードを取得
     *
     * @return LengthAwarePaginator
     */  
    public function getAll(): LengthAwarePaginator;


    /**
     * バルクインサート
     * テーブルに保存・更新する
     * 
     * @param array $data
     * @return void
     */
    public function bulkInsertOrUpdate($data): void;


    /**
     * brand_idを元にyoutube動画を取得する
     *
     * @param array $brand_ids
     * @return LengthAwarePaginator
     */
    public function getBrandVideosByPlayerIds( array $brand_ids ): LengthAwarePaginator;
}