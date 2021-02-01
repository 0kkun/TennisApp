<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\RankingRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Services\Api\ApiServiceInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RankingController extends Controller
{
    private $ranking_repository;
    private $api_service;
    private $result_status;

    // レスポンスのフォーマット
    protected $response;


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
     * @return JsonResponse
     */
    public function fetchRankings(Request $request): JsonResponse
    {
        try {
            $start = microtime(true);
            Log::info("[START] " . __FUNCTION__ );

            $is_varidation_error = $this->checkValidationError(__FUNCTION__, $request->all());
            $status = $this->getStatusCode($is_varidation_error);

            if ($status === $this->result_status['success']) {

                $num = $request->num;
                $rankings = $this->ranking_repository->fetchRankings($num);

                if ($rankings->isEmpty()) {
                    $status =  $this->result_status['no_content'];
                    $rankings = '';
                }
                $this->response = ['status' => $status, 'data' => $rankings];

            } else {
                $this->response = ['status' => $status, 'data' => ''];
            }

            $end = microtime(true);
            $time = $this->calcTime($start, $end);
            Log::info("[ END ] " . __FUNCTION__ . ", STATUS:" . $status . ", 処理時間:" . $time . "秒");
            return response()->json($this->response);

        } catch (\Exception $e) {
            Log::info("[Exception]" . __FUNCTION__ . $e->getMessage());
            $this->respose = $this->handlingException($e);
            return response()->json($this->response);
        }
    }


    /**
     * Exception発生時のエラーをレスポンスにまとめる
     *
     * @param \Exception $e
     * @return array
     */
    private function handlingException(\Exception $e): array
    {
        $status = $this->result_status['server_error'];
        $error_info = $this->api_service->makeErrorInfo($e);
        return $this->response = ['status' => $status, 'data' => $error_info];
    }


    /**
     * 処理にかかった時間を算出し桁数調整する
     *
     * @return void
     */
    private function calcTime($start, $end): float
    {
        return substr(($end - $start), 0 ,7);
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
            'fetchRankings' => [
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
}
