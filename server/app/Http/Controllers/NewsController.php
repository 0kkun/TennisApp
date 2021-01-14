<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\NewsArticlesRepository;
use App\Repositories\Contracts\BrandNewsArticlesRepository;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    private $news_article_repository;
    private $brand_news_article_repository;

    /**
     * リポジトリをDI
     * 
     */
    public function __construct(
        NewsArticlesRepository $news_article_repository,
        BrandNewsArticlesRepository $brand_news_article_repository
    )
    {
        $this->news_article_repository = $news_article_repository;
        $this->brand_news_article_repository = $brand_news_article_repository;
    }


    public function index()
    {
        return view('news.index');
    }
}
