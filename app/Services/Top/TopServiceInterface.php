<?php

namespace App\Services\Top;

use Illuminate\Support\Collection;

interface TopServiceInterface
{

    /**
     * ユーザーがお気に入りに登録した選手の名前に紐づいたニュース記事を取得する
     *
     * @return 
     */
    public function getArticleByFavoritePlayer();
}