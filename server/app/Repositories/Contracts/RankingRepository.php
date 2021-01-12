<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface RankingRepository
{
    /**
     * バルクインサート
     * テーブルに保存・更新する
     * 
     * @param array|Collection $data
     * @return void
     */
    public function bulkInsertOrUpdate($data): void;
}