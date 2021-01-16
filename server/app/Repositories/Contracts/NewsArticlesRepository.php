<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface NewsArticlesRepository
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
     * 選手名を元に記事を取得する
     *
     * @param array $player_names
     * @param bool $is_paginate
     * @return Collection|LengthAwarePaginator
     */
    public function getArticleByPlayerNames(array $player_names, bool $is_paginate);


    /**
     * ニュース記事を全て取得する
     *
     * @param boolean $is_paginate
     * @return Collection|LengthAwarePaginator
     */
    public function getAllArticles(bool $is_paginate);
}