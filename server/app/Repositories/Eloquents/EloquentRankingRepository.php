<?php

namespace App\Repositories\Eloquents;

use App\Repositories\Contracts\RankingRepository;
use App\Models\Ranking;
use Illuminate\Support\Collection;

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


    /**
     * 指定した数ランキングのレコードを高い順に取得する
     * 上から順に取得するのがデフォルトなのでソートはしない
     *
     * @param integer|null $num
     * @return Collection
     */
    public function fetchRankings(?int $num=100): Collection
    {
        return $this->rankings
                    ->limit($num)
                    ->get();
    }
}