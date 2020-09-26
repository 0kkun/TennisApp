<?php

namespace App\Repositories\Eloquents;

use App\Models\Player;
use App\Repositories\Contracts\PlayersRepository;
use Illuminate\Support\Collection;

class EloquentPlayersRepository implements PlayersRepository
{
    protected $players;


    /**
    * @param object $players
    */
    public function __construct(
        Player $players
    )
    {
        $this->players = $players;
    }


    /**
     * 全レコード取得
     *
     * @return void
     */
    public function getAll(): Collection
    {
        return $this->players
                    ->get();
    }


    /**
     * バルクインサート処理
     *
     * @param  Collection|Players|array $data
     * @return void
     */
    public function bulkInsertOrUpdate($data): void
    {
        $this->players->bulkInsertOrUpdate($data);
    }


    /**
     * 検索機能
     *
     * @param array $inputs
     * @return Collection
     */
    public function searchPlayers( array $inputs ): Collection
    {
        $name = $inputs['name'] ?? null;
        $country = $inputs['country'] ?? null;
        $age = $inputs['age'] ?? null;

        return $this->players
                    ->when( !empty($name) , function ($query) use ($name) {
                        $query->orWhere( 'name_jp', 'like', '%' . $name . '%' )
                                ->orWhere( 'name_en', 'like', '%' . $name . '%' );
                    })
                    ->when( !empty($country) , function ($query) use ($country) {
                        $query->where( 'country', $country );
                    })
                    ->when( !empty($age) , function ($query) use ($age) {
                        if($age == 19) {
                            $query->where( 'age', '<', 20 );
                        } else if ($age == 20) {
                            $query->whereBetween( 'age', [20, 29] );
                        } else if ($age == 30) {
                            $query->whereBetween( 'age', [30, 39] );
                        }  else if ($age == 40) {
                            $query->where( 'age', '>=', 40 );
                        }
                    })
                    ->get();
    }

    /**
     * 全ての国名を取得し、重複削除して一意にして返す
     *
     * @return array
     */
    public function getAllCountryNames(): array
    {
        $country_all = $this->players
                            ->get()
                            ->pluck('country')
                            ->toArray();

        return array_unique($country_all);
    }
}