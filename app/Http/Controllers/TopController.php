<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\PlayersRepository;
use App\Repositories\Contracts\AtpRankingsRepository;
use App\Services\Top\TopServiceInterface;
use App\Repositories\Contracts\TourInformationsRepository;

use Carbon\Carbon;

class TopController extends Controller
{
    private $atp_rankings_repository;
    private $top_service;
    private $tour_informations_repository;


    /**
     * リポジトリをDI
     * 
     * @param PlayersRepository $players_repository
     */
    public function __construct(
        AtpRankingsRepository $atp_rankings_repository,
        TopServiceInterface $top_service,
        TourInformationsRepository $tour_informations_repository
    )
    {
        $this->atp_rankings_repository = $atp_rankings_repository;
        $this->top_service = $top_service;
        $this->tour_informations_repository = $tour_informations_repository;
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

        $tour_informations = $this->tour_informations_repository->getAll()->toArray();


        $today = Carbon::today();

        return view('top.index', compact(
            'atp_rankings',
            'news_articles',
            'tour_informations',
            'today'
        ));
    }
}
