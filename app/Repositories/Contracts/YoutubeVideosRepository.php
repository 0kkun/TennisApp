<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface YoutubeVideosRepository
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
}