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


    /**
     * 指定した数ランキングのレコードを高い順に取得する
     *
     * @param integer|null $num
     * @return Collection
     */
    public function fetchRankings(?int $num=100): Collection;
}