<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\RankingRepository;
use App\Services\Api\ApiServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AnalysisController extends Controller
{
    private $ranking_repository;
    private $api_service;

    // レスポンスのフォーマット
    protected $response;
    protected $result_status;

    /**
     * Constructor
     *
     * @param RankingRepository $ranking_repository
     * @param ApiServiceInterface $api_service
     */
    public function __construct(
        RankingRepository $ranking_repository,
        ApiServiceInterface $api_service
    )
    {
        $this->response = config('api_template.response_format');
        $this->result_status = config('api_template.result_status');
        $this->ranking_repository = $ranking_repository;
        $this->api_service = $api_service;
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
     * @return JsonResponse
     */
    public function fetchAgeAnalysis(Request $request): JsonResponse
    {
        try {
            $start = microtime(true);
            Log::info("[START] " . __FUNCTION__ );

            // リクエストの中身をチェック
            $is_varidation_error = $this->checkValidationError(__FUNCTION__, $request->all());
            $status = $this->getStatusCode($is_varidation_error);

            if ($status === $this->result_status['success']) {

                $num = $request->input('num');

                $rankings = $this->ranking_repository->fetchRankings($num)->toArray();

                $rankings_by_age = $this->arrangeByAge($rankings);

                $average_rank = $this->makeAverageRankByAge($rankings_by_age);

                $count_player = $this->makeCountPlayer($rankings_by_age);

                $analysis_data = [
                    'average_rank' => $average_rank,
                    'count_player' => $count_player
                ];

                $this->response = ['status' => $status, 'data' => $analysis_data];
            } else {
                $this->response = ['status' => $status, 'data' => ''];
            }

            $end = microtime(true);
            $time = $this->api_service->calcTime($start, $end);
            Log::info("[ END ] " . __FUNCTION__ . ", STATUS:" . $status . ", 処理時間:" . $time . "秒");

        } catch (\Exception $e) {
            Log::info("[Exception]" . __FUNCTION__ . $e->getMessage());
            $this->respose = $this->api_service->makeErrorResponse($e);
        }

        return response()->json($this->response);
    }


    /**
     * バリデーションエラーか判定する
     *
     * @param string $func_name
     * @param array $check_keys
     * @return boolean
     */
    private function checkValidationError(string $func_name, array $check_keys): bool
    {
        $func_and_keys_pattern = [
            'fetchAgeAnalysis' => [
                'num' => 'required|integer'
            ],
        ];
        $validator = Validator::make($check_keys, $func_and_keys_pattern[$func_name]);

        $is_validation_error = !empty($validator->errors()->messages());

        return $is_validation_error;
    }


    /**
     * バリデーションチェックの結果に基づくステータスコードを取得
     *
     * @param boolean $is_validation_error
     * @return integer
     */
    private function getStatusCode(bool $is_validation_error): int
    {
        return $is_validation_error ? $this->result_status['bad_request'] : $this->result_status['success'];
    }


    /**
     * 年代ごとにプレイヤーをまとめる
     *
     * @param array $rankings
     * @return array
     */
    private function arrangeByAge(array $rankings): array
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
     * 年齢ごとのランキング平均値を作成する
     *
     * @param array $rankings_by_age
     * @return array
     */
    private function makeAverageRankByAge(array $rankings_by_age): array
    {
        $average_rank['10s'] = $this->calcAveRank($rankings_by_age['10s']);
        $average_rank['20s'] = $this->calcAveRank($rankings_by_age['20s']);
        $average_rank['30s'] = $this->calcAveRank($rankings_by_age['30s']);
        $average_rank['40s'] = $this->calcAveRank($rankings_by_age['40s']);

        return $average_rank;
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


    /**
     * 年代ごとの選手の人数配列を作成する
     *
     * @param array $rankings_by_age
     * @return array
     */
    private function makeCountPlayer(array $rankings_by_age): array
    {
        $count_player['10s'] = count($rankings_by_age['10s']);
        $count_player['20s'] = count($rankings_by_age['20s']);
        $count_player['30s'] = count($rankings_by_age['30s']);
        $count_player['40s'] = count($rankings_by_age['40s']); 

        return $count_player;
    }
}
