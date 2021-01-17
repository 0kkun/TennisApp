<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\TourScheduleRepository;
use Exception;

class TourScheduleController extends Controller
{
    private $tour_schedule_repository;

    /**
     * リポジトリをDI
     *
     * @param TourScheduleRepository $tour_schedule_repository
     */
    public function __construct(
        TourScheduleRepository $tour_schedule_repository
    )
    {
        $this->tour_schedule_repository = $tour_schedule_repository;
    }


    /**
     * [API] ツアースケジュール取得用メソッド
     *
     * @param Request $request
     * @return Json|Exception
     */
    public function fetchTourSchedules(Request $request)
    {
        try {
            $num = $request->input('num');
            $is_paginate = false;

            $response = $this->tour_schedule_repository->getAll($num, $is_paginate);

            return request()->json(200, $response);

        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}