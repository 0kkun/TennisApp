<?php

namespace App\Repositories\Eloquents;

use App\Repositories\Contracts\PlayersNewsArticleRepository;
use App\Models\PlayersNewsArticle;

class EloquentPlayersNewsArticleRepository implements PlayersNewsArticleRepository
{
    protected $players_news_articles;


    /**
    * @param object $news_articles
    */
    public function __construct(
        PlayersNewsArticle $players_news_articles
    )
    {
        $this->players_news_articles = $players_news_articles;
    }


    /**
     * バルクインサート処理
     *
     * @param  Collection|PlayersNewsArticle|array $data
     * @return void
     */
    public function bulkInsertOrUpdate($data): void
    {
        $this->players_news_articles->bulkInsertOrUpdate($data);
    }


    /**
     * 指定の数、記事を取得する
     *
     * @param integer $num
     * @param bool $is_paginate
     * @return Collection|LengthAwarePaginator
     */
    public function fetchArticles(int $num, bool $is_paginate)
    {
        return $this->players_news_articles
                    ->limit($num)
                    ->orderBy('post_time', 'desc')
                    ->when($is_paginate, function ($query) {
                        // パラメータ名を指定することでページネーションを独立させる
                        return $query->paginate(config('const.PAGINATE.NEWS_LINK_NUM'), ["*"], 'newspage'); 
                    }, function ($query) {
                        return $query->get();
                    });
    }

    /**
     * like句で選手名で検索し記事を取得する
     *
     * @param array $player_names
     * @param integer $num
     * @param boolean $is_paginate
     * @return Collection|LengthAwarePaginator
     */
    public function fetchArticlesByPlayerNames(array $player_names, int $num, bool $is_paginate)
    {
        return $this->players_news_articles
                    ->where( function ($query) use ($player_names) {
                        for ($i=0; $i<count($player_names); $i++) {
                            $query->orWhere('title', 'like', '%' . $player_names[$i] . '%');
                        }
                    })
                    ->limit($num)
                    ->orderBy('post_time', 'desc')
                    ->when($is_paginate, function ($query) {
                        // パラメータ名を指定することでページネーションを独立させる
                        return $query->paginate(config('const.PAGINATE.NEWS_LINK_NUM'), ["*"], 'newspage'); 
                    }, function ($query) {
                        return $query->get();
                    });
    }
}