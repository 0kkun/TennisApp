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
}