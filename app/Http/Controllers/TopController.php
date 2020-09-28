<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\PlayersRepository;
use App\Repositories\Contracts\AtpRankingsRepository;
use App\Repositories\Contracts\NewsArticlesRepository;

class TopController extends Controller
{
    private $atp_rankings_repository;
    private $news_articles_repository;

    /**
     * リポジトリをDI
     * 
     * @param PlayersRepository $players_repository
     */
    public function __construct(
        AtpRankingsRepository $atp_rankings_repository,
        NewsArticlesRepository $news_articles_repository

    )
    {
        $this->atp_rankings_repository = $atp_rankings_repository;
        $this->news_articles_repository = $news_articles_repository;
    }


    /**
     * トップページ遷移
     *
     * @return void
     */
    public function index()
    {
        $atp_rankings = $this->atp_rankings_repository->getAll()->toArray();
        $news_articles = $this->news_articles_repository->getAll();

        return view('top.index', compact('atp_rankings', 'news_articles'));
    }
}
