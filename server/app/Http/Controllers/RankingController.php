<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\RankingRepository;
use Exception;
use Illuminate\Support\Facades\Auth;

class RankingController extends Controller
{
    private $ranking_repository;


    /**
     * リポジトリをDI
     *
     * @param RankingRepository $ranking_repository
     */
    public function __construct(
        RankingRepository $ranking_repository
    )
    {
        $this->ranking_repository = $ranking_repository;
    }


    /**
     * 新しいデザインのランキングトップ画面
     */
    public function top()
    {
        if (Auth::check()) {
            return view('ranking.top');
        } else {
            return view('top.index');
        }
    }


    /**
     * [API] ランキング取得メソッド
     *
     * @param Request $request
     * @return Json|Exception
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
