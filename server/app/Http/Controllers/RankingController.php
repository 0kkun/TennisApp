<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\RankingRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Services\Api\ApiServiceInterface;
use App\Modules\BatchLogger;

class RankingController extends Controller
{
    private $ranking_repository;
    private $api_service;
    private $logger;

    // レスポンスのフォーマット
    protected $response;
    protected $result_status;

    /**
     * リポジトリをDI
     *
     * @param RankingRepository $ranking_repository
     * @param ApiServiceInterface $api_service
     */
    public function __construct(
        RankingRepository $ranking_repository,
        ApiServiceInterface $api_service
    )
    {
        $this->logger = new BatchLogger(__CLASS__);
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
            // リクエストの中身をチェック
            $expected_key = ['num'];
            $status = $this->api_service->checkArgs($request, $expected_key);

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

            $this->logger->write('status code :' . $status, 'info');
            $this->logger->success();

            return response()->json($this->response);

        } catch (\Exception $e) {
            $this->logger->exception($e);
            $status = $this->result_status['server_error'];
            $error_info = $this->api_service->makeErrorInfo($e);
            $this->response = ['status' => $status,'data' => $error_info];

            return response()->json($this->response);
        }
    }
}
