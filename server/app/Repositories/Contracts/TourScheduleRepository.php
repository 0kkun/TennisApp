<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface TourScheduleRepository
{
    /**
     * 開催中の大会とこれから始まる予定の大会だけを取得する
     * すでに終了した大会は取得しない
     *
     * @param integer $num
     * @param bool $is_paginate
     * @return Collection|LengthAwarePaginator
     */  
    public function getAll(int $num, bool $is_paginate);


    /**
     * バルクインサート
     * テーブルに保存・更新する
     * 
     * @param array|Model|Collection $data
     * @return void
     */
    public function bulkInsertOrUpdate($data): void;
}