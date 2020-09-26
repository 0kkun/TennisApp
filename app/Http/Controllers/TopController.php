<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\PlayersRepository;
use App\Repositories\Contracts\AtpRankingsRepository;

class TopController extends Controller
{
    private $atp_rankings_repository;

    /**
     * リポジトリをDI
     * 
     * @param PlayersRepository $players_repository
     */
    public function __construct(
        AtpRankingsRepository $atp_rankings_repository
    )
    {
        $this->atp_rankings_repository = $atp_rankings_repository;
    }


    /**
     * トップページ遷移
     *
     * @return void
     */
    public function index()
    {
        $atp_rankings = $this->atp_rankings_repository->getAll()->toArray();

        return view('top.index', compact('atp_rankings'));
    }
}
