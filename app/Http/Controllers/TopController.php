<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\PlayersRepository;
use App\Repositories\Contracts\AtpRankingsRepository;
use App\Repositories\Contracts\NewsArticlesRepository;
use App\Repositories\Contracts\FavoritePlayersRepository;
use App\Services\Top\TopServiceInterface;

class TopController extends Controller
{
    private $atp_rankings_repository;
    private $news_articles_repository;
    private $favorite_players_repository;
    private $top_service;

    /**
     * リポジトリをDI
     * 
     * @param PlayersRepository $players_repository
     */
    public function __construct(
        AtpRankingsRepository $atp_rankings_repository,
        NewsArticlesRepository $news_articles_repository,
        FavoritePlayersRepository $favorite_players_repository,
        TopServiceInterface $top_service

    )
    {
        $this->atp_rankings_repository = $atp_rankings_repository;
        $this->news_articles_repository = $news_articles_repository;
        $this->favorite_players_repository = $favorite_players_repository;
        $this->top_service = $top_service;
    }


    /**
     * トップページ遷移
     *
     * @return void
     */
    public function index()
    {
        $atp_rankings = $this->atp_rankings_repository->getAll()->toArray();

        $news_articles = $this->top_service->getArticleByFavoritePlayer();

        return view('top.index', compact('atp_rankings', 'news_articles'));
    }


}
