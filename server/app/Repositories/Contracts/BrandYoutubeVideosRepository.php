<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface BrandYoutubeVideosRepository
{
    /**
     * 全レコードを取得
     *
     * @param integer $num
     * @param boolean $is_paginate
     * @return mixed - Collection | LengthAwarePaginator
     */
    public function getAll(int $num, bool $is_paginate);


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
     * @param integer $num
     * @param array $brand_ids
     * @param boolean $is_paginate
     * @return mixed - Collection | LengthAwarePaginator
     */
    public function getVideosByBrandIds(int $num, array $brand_ids, bool $is_paginate);
}