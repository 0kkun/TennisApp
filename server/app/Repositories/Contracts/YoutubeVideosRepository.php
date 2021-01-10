<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface YoutubeVideosRepository
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
     * player_idを元にyoutube動画を取得する
     *
     * @param array $player_ids
     * @return LengthAwarePaginator
     */
    public function getVideosByPlayerIds( array $player_ids ): LengthAwarePaginator;
}