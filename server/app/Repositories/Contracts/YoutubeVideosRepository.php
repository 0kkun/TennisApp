<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface YoutubeVideosRepository
{
    /**
     * 全レコードを取得
     * 
     * @param integer $num
     * @param bool $is_paginate
     * @return LengthAwarePaginator|Collection
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
     * player_idを元にyoutube動画を取得する
     *
     * @param integer $num
     * @param array $player_ids
     * @param bool $is_paginate
     * @return LengthAwarePaginator|Collection
     */
    public function getVideosByPlayerIds(int $num, array $player_ids, bool $is_paginate);
}