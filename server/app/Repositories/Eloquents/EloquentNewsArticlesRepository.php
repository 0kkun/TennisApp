<?php

namespace App\Repositories\Eloquents;

use App\Models\NewsArticle;
use App\Repositories\Contracts\NewsArticlesRepository;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentNewsArticlesRepository implements NewsArticlesRepository
{
    protected $news_articles;


    /**
    * @param object $news_articles
    */
    public function __construct(
        NewsArticle $news_articles
    )
    {
        $this->news_articles = $news_articles;
    }


    /**
     * 全レコード取得
     *
     * @return LengthAwarePaginator
     */
    public function getAll(): LengthAwarePaginator
    {
        return $this->news_articles
                    ->orderBy('id', 'desc')
                    ->paginate(config('const.PAGINATE.NEWS_LINK_NUM'));
    }


    /**
     * バルクインサート処理
     *
     * @param  Collection|news_articles|array $data
     * @return void
     */
    public function bulkInsertOrUpdate($data): void
    {
        $this->news_articles->bulkInsertOrUpdate($data);
    }


    /**
     * 選手名を元に記事を取得する
     *
     * @param array $player_names
     * @param bool $is_paginate
     * @return Collection|LengthAwarePaginator
     */
    public function getArticleByPlayerNames(array $player_names, bool $is_paginate)
    {
        $num = config('const.PAGINATE.NEWS_LINK_NUM');

        return $this->news_articles
                    ->where( function ($query) use ($player_names) {
                        for ($i=0; $i<count($player_names); $i++) {
                            $query->orWhere('title', 'like', '%' . $player_names[$i] . '%');
                        }
                    })
                    ->orderBy('post_time', 'desc')
                    ->when($is_paginate, function ($query) {
                        // パラメータ名を指定することでページネーションを独立させる
                        return $query->paginate(config('const.PAGINATE.NEWS_LINK_NUM'), ["*"], 'newspage'); 
                    }, function ($query) {
                        return $query->limit(50)->get();
                    });
    }


    /**
     * ニュース記事を全て取得する
     *
     * @param boolean $is_paginate
     * @return Collection|LengthAwarePaginator
     */
    public function getAllArticles(bool $is_paginate)
    {
        return $this->news_articles
                    ->orderBy('id', 'desc')
                    ->when($is_paginate, function ($query) {
                        return $query->paginate(config('const.PAGINATE.NEWS_LINK_NUM'));
                    }, function ($query) {
                        return $query->get();
                    });
    }
}