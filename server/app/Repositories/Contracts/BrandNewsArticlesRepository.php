<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BrandNewsArticlesRepository
{
    /**
     * 全レコードを取得
     *
     * @param integer $num
     * @param boolean $is_paginate
     * @return Collection|LengthAwarePaginator
     */
    public function fetchArticles(int $num, bool $is_paginate);


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
     * @param integer $num
     * @param boolean $is_paginate
     * @return Collection|LengthAwarePaginator
     */
    public function fetchArticlesByBrandNames(array $brand_names, int $num, bool $is_paginate);
}