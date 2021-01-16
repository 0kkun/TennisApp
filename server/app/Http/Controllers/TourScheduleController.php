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
     * ツアースケジュール取得用API
     *
     * @return void
     */
    public function fetchTourSchedules()
    {
        try {
            $is_paginate = false;
            $response = $this->tour_schedule_repository->getAll($is_paginate);
            return request()->json(200, $response);

        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
