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

    /**
     * 指定の数、記事を取得する
     *
     * @param integer $num
     * @param bool $is_paginate
     * @return Collection|LengthAwarePaginator
     */
    public function fetchArticles(int $num, bool $is_paginate);

    /**
     * like句で選手名で検索し記事を取得する
     *
     * @param array $player_names
     * @param integer $num
     * @param boolean $is_paginate
     * @return Collection|LengthAwarePaginator
     */
    public function fetchArticlesByPlayerNames(array $player_names, int $num, bool $is_paginate);
}