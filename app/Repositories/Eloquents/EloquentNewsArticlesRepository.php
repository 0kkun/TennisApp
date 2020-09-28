<?php

namespace App\Repositories\Eloquents;

use App\Models\NewsArticle;
use App\Repositories\Contracts\NewsArticlesRepository;
use Illuminate\Support\Collection;

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
     * @return mixed
     */
    public function getAll()
    {
        return $this->news_articles
                    ->orderBy('id', 'desc')
                    ->paginate(20);
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
}