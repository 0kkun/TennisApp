<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface PlayersNewsArticleRepository
{
    /**
     * バルクインサート
     * テーブルに保存・更新する
     * 
     * @param array $data
     * @return void
     */
    public function bulkInsertOrUpdate($data): void;
}