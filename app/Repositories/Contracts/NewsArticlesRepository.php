<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface NewsArticlesRepository
{
    /**
     * 全レコードを取得
     *
     * @return mixed
     */  
    public function getAll();


    /**
     * バルクインサート
     * テーブルに保存・更新する
     * 
     * @param array $data
     * @return void
     */
    public function bulkInsertOrUpdate($data): void;
}