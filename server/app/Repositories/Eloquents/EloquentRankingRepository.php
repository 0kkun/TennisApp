<?php

namespace App\Repositories\Eloquents;

use App\Repositories\Contracts\RankingRepository;
use App\Models\Ranking;

class EloquentRankingRepository implements RankingRepository
{
    protected $rankings;

    /**
     * @param Ranking $rankings
     */
    public function __construct(
        Ranking $rankings
    )
    {
        $this->rankings = $rankings;
    }

    /**
     * バルクインサート処理
     *
     * @param  Collection|array $data
     * @return void
     */
    public function bulkInsertOrUpdate($data): void
    {
        $this->rankings->bulkInsertOrUpdate($data);
    }
}