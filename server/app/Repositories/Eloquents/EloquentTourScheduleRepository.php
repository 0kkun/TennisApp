<?php

namespace App\Repositories\Eloquents;

use App\Repositories\Contracts\TourScheduleRepository;
use App\Models\TourSchedule;
use Carbon\Carbon;

class EloquentTourScheduleRepository implements TourScheduleRepository
{
    protected $tour_schedules;

    /**
    * @param object $tour_schedules
    */
    public function __construct(
        TourSchedule $tour_schedules
    )
    {
        $this->tour_schedules = $tour_schedules;
    }

    /**
     * 開催中の大会とこれから始まる予定の大会だけを取得する
     * すでに終了した大会は取得しない
     *
     * @param integer $num
     * @param bool $is_paginate
     * @return Collection
     */
    public function getAll(int $num, bool $is_paginate)
    {
        $today = Carbon::today();

        return $this->tour_schedules
                    ->where('end_date', '>=', $today)
                    ->orderBy('start_date', 'asc')
                    ->when($is_paginate, function ($query) {
                        return $query->paginate(config('const.PAGINATE.NEWS_LINK_NUM'), ["*"], 'tour'); 
                    }, function ($query) use ($num) {
                        return $query->limit($num)->get();
                    });
    }

    /**
     * バルクインサート処理
     *
     * @param  Collection|Model|array $data
     * @return void
     */
    public function bulkInsertOrUpdate($data): void
    {
        $this->tour_schedules->bulkInsertOrUpdate($data);
    }
}