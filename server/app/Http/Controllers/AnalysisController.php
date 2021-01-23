<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\RankingRepository;
use Exception;

class AnalysisController extends Controller
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


    public function index()
    {
        return view('analysis.index');
    }


    /**
     * ランキング分析用データ取得
     * 20台、30台、40台の平均ランキングを返す
     * 年代別の人数も返す
     * 
     * URL
     * http://localhost:10080/api/v1/analysis_age
     *
     * @param Request $request
     * @return Json
     */
    public function fetchAgeAnalysis(Request $request)
    {
        try {
            $num = $request->input('num');

            $rankings = $this->ranking_repository->fetchRankings($num)->toArray();

            $data = $this->arrangeByAge($rankings);

            $average_rank['10s'] = $this->calcAveRank($data['10s']);
            $average_rank['20s'] = $this->calcAveRank($data['20s']);
            $average_rank['30s'] = $this->calcAveRank($data['30s']);
            $average_rank['40s'] = $this->calcAveRank($data['40s']);

            $count_player['10s'] = count($data['10s']);
            $count_player['20s'] = count($data['20s']);
            $count_player['30s'] = count($data['30s']);
            $count_player['40s'] = count($data['40s']); 

            $response = [
                'average_rank' => $average_rank,
                'count_player' => $count_player
            ];

            return response()->json($response, 200);

        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }


    /**
     * 年代ごとにプレイヤーをまとめる
     *
     * @param array $rankings
     * @return void
     */
    private function arrangeByAge(array $rankings)
    {
        $data = ['10s'=>[], '20s'=>[], '30s'=>[], '40s'=>[]];

        foreach($rankings as $ranking)
        {
            if ( $ranking['age'] >= 10 && $ranking['age'] < 20 ) {
                $data['10s'][] = $ranking;
            } elseif ( $ranking['age'] >= 20 && $ranking['age'] < 30 ) {
                $data['20s'][] = $ranking;
            } elseif ( $ranking['age'] >= 30 && $ranking['age'] < 40 ) {
                $data['30s'][] = $ranking;
            } elseif ( $ranking['age'] >= 40 && $ranking['age'] < 50 ) {
                $data['40s'][] = $ranking;
            }
        }
        return $data;
    }


    /**
     * ランキングの平均値を算出する
     *
     * @param array $data
     * @return integer
     */
    private function calcAveRank(array $data): int
    {
        $count = count($data);

        if ($count !== 0) {
            $rank_sum = 0;
            for ($i=0; $i < $count; $i++) {
                $rank_sum += $data[$i]['rank'];
            }
            return $rank_sum / $count;
        } else {
            return 0;
        }
    }
}
