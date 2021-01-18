<?php

namespace App\Repositories\Eloquents;

use App\Models\BrandNewsArticle;
use App\Repositories\Contracts\BrandNewsArticlesRepository;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentBrandNewsArticlesRepository implements BrandNewsArticlesRepository
{
    protected $news_articles;


    /**
    * @param object $brand_news_articles
    */
    public function __construct(
        BrandNewsArticle $brand_news_articles
    )
    {
        $this->brand_news_articles = $brand_news_articles;
    }


    /**
     * 全レコードを取得
     *
     * @param integer $num
     * @param boolean $is_paginate
     * @return Collection|LengthAwarePaginator
     */
    public function fetchArticles(int $num, bool $is_paginate)
    {
        return $this->brand_news_articles
                    ->orderBy('post_time', 'desc')
                    ->when($is_paginate, function ($query) {
                        // パラメータ名を指定することでページネーションを独立させる
                        return $query->paginate(config('const.PAGINATE.NEWS_LINK_NUM'), ["*"], 'brandnewspage'); 
                    }, function ($query) use ($num) {
                        return $query->limit($num)->get();
                    });
    }


    /**
     * バルクインサート処理
     *
     * @param  Collection|brand_news_articles|array $data
     * @return void
     */
    public function bulkInsertOrUpdate($data): void
    {
        $this->brand_news_articles->bulkInsertOrUpdate($data);
    }


    /**
     * ブランド名を元に記事を取得する
     *
     * @param array $brand_names
     * @param integer $num
     * @param boolean $is_paginate
     * @return Collection|LengthAwarePaginator
     */
    public function fetchArticlesByBrandNames(array $brand_names, int $num, bool $is_paginate)
    {
        return $this->brand_news_articles
                    ->whereIn('brand_name', $brand_names)
                    ->orderBy('post_time', 'desc')
                    ->when($is_paginate, function ($query) {
                        // パラメータ名を指定することでページネーションを独立させる
                        return $query->paginate(config('const.PAGINATE.NEWS_LINK_NUM'), ["*"], 'brandnewspage'); 
                    }, function ($query) use ($num) {
                        return $query->limit($num)->get();
                    });
    }
}