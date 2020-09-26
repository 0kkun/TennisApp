<?php

namespace App\Repositories\Eloquents;

use App\Models\AtpRanking;
use App\Repositories\Contracts\AtpRankingsRepository;
use Illuminate\Support\Collection;

class EloquentAtpRankingsRepository implements AtpRankingsRepository
{
    protected $atp_rankings;


    /**
    * @param object $atp_rankings
    */
    public function __construct(
      AtpRanking $atp_rankings
  )
  {
      $this->atp_rankings = $atp_rankings;
  }


    /**
     * 最近の日付のランキングを全て取得
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        $latest_date = $this->atp_rankings
                            ->max('ymd');

        return $this->atp_rankings
                    ->where('ymd', '=', $latest_date)
                    ->get();
    }


    /**
     * バルクインサート処理
     *
     * @param  Collection|FavoritePlayers|array $data
     * @return void
     */
    public function bulkInsertOrUpdate($data): void
    {
      $this->atp_rankings->bulkInsertOrUpdate($data);
    }
}