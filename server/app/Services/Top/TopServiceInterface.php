<?php

namespace App\Services\Top;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface TopServiceInterface
{

    /**
     * ユーザーがお気に入りに登録した選手の名前に紐づいたニュース記事を取得する
     *
     * @return LengthAwarePaginator
     */
    public function getArticleByFavoritePlayer();


    /**
     * ユーザーがお気に入りに登録したブランドの名前に紐づいたニュース記事を取得する
     *
     * @return LengthAwarePaginator
     */
    public function getArticleByFavoriteBrand();


    /**
     * お気に入り選手に紐づいたyoutube動画を取得する
     *
     * @return LengthAwarePaginator
     */
    public function getVideosByFavoritePlayer();


    /**
     * お気に入りブランドに紐づいたyoutube動画を取得する
     *
     * @return LengthAwarePaginator
     */
    public function getVideosByFavoriteBrand();
}