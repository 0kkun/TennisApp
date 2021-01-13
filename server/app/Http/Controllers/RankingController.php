<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\RankingRepository;
use Exception;

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

    /**
     * ランキング取得用のAPI
     *
     * @param Request $request
     * @return void
     */
    public function fetchRankings(Request $request)
    {
        try {
            $num = $request->input('num');
            $response = $this->ranking_repository->fetchRankings($num);
            return request()->json(200, $response);

        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
