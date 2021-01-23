<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\TourScheduleRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Services\Api\ApiServiceInterface;
use App\Modules\BatchLogger;

class TourScheduleController extends Controller
{
    private $tour_schedule_repository;
    private $api_service;
    private $logger;

    // レスポンスのフォーマット
    protected $response;
    protected $result_status;

    /**
     * リポジトリをDI
     *
     * @param TourScheduleRepository $tour_schedule_repository
     * @param ApiServiceInterface $api_service
     */
    public function __construct(
        TourScheduleRepository $tour_schedule_repository,
        ApiServiceInterface $api_service
    )
    {
        $this->logger = new BatchLogger('FavoritePlayerController');
        $this->response = config('api_template.response_format');
        $this->result_status = config('api_template.result_status');
        $this->tour_schedule_repository = $tour_schedule_repository;
        $this->api_service = $api_service;
    }


    /**
     * [API] ツアースケジュール取得用メソッド
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function fetchTourSchedules(Request $request): JsonResponse
    {
        try {
            // リクエストの中身をチェック
            $expected_key = ['num'];
            $status = $this->api_service->checkArgs($request, $expected_key);

            if ($status === $this->result_status['success']) {

                $num = $request->input('num');
                $is_paginate = false;

                $tour_schedules = $this->tour_schedule_repository->getAll($num, $is_paginate);

                $this->response = ['status' => $status, 'data' => $tour_schedules];

            } else {
                $this->response = ['status' => $status, 'data' => ''];
            }

            $this->logger->write('status code :' . $status, 'info');
            $this->logger->success();

            return response()->json($this->response);

        } catch (Exception $e) {
            $this->logger->exception($e);
            $status = $this->result_status['server_error'];
            $error_info = $this->api_service->makeErrorInfo($e);
            $this->response = ['status' => $status,'data' => $error_info];

            return response()->json($this->response);
        }
    }
}
