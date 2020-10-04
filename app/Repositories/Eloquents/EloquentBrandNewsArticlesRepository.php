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
     * 全レコード取得
     *
     * @return LengthAwarePaginator
     */
    public function getAll(): LengthAwarePaginator
    {
        return $this->brand_news_articles
                    ->orderBy('id', 'desc')
                    ->paginate(20);
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
     *TODO: お気に入りブランド名が無い場合エラーが出るので対策が必要
     * @param array $brand_names
     * @return LengthAwarePaginator
     */
    public function getArticleByBrandNames( array $brand_names): LengthAwarePaginator
    {
        return $this->brand_news_articles
                    ->where( function ($query) use ($brand_names) {
                        for ($i=0; $i<count($brand_names); $i++) {
                            $query->orWhere('brand_name', $brand_names[$i]);
                        }
                    })
                    ->orderBy('post_time', 'desc')
                    ->paginate(20);
    }
}