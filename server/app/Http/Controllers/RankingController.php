<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\RankingRepository;

class RankingController extends Controller
{
    private $ranking_repository;

    /**
     * リポジトリをDI
     * 
     */
    public function __construct(
        RankingRepository $ranking_repository
    )
    {
        $this->ranking_repository = $ranking_repository;
    }


    public function index()
    {
        return view('ranking.index');
    }
}
