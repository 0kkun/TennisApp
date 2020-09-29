<?php

namespace App\Repositories\Eloquents;

use App\Models\TourInformation;
use App\Repositories\Contracts\TourInformationsRepository;
use Illuminate\Support\Collection;

class EloquentTourInformationsRepository implements TourInformationsRepository
{
    protected $tour_informations;


    /**
    * @param object $tour_informations
    */
    public function __construct(
        TourInformation $tour_informations
    )
    {
        $this->tour_informations = $tour_informations;
    }


    /**
     * 全レコード取得
     *
     * @return void
     */
    public function getAll(): Collection
    {
        return $this->tour_informations
                    ->get();
    }


    /**
     * バルクインサート処理
     *
     * @param  Collection|TourInformations|array $data
     * @return void
     */
    public function bulkInsertOrUpdate($data): void
    {
        $this->tour_informations->bulkInsertOrUpdate($data);
    }
}