<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BrandNewsArticlesRepository
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
     * ブランド名を元に記事を取得する
     *
     * @param array $brand_names
     * @return LengthAwarePaginator
     */
    public function getArticleByBrandNames(array $brand_names): LengthAwarePaginator;
}