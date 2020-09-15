<?php

namespace App\Repositories\Eloquents;

use App\Models\Player;
use App\Repositories\Contracts\PlayersRepository;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EloquentPlayersRepository implements PlayersRepository
{
    protected $players_repository;


    /**
    * @param object $players_repository
    */
    public function __construct(
        Player $players_repository
    )
    {
        $this->players_repository = $players_repository;
    }


    /**
     * 全レコード取得
     *
     * @return void
     */
    public function getAll(): Collection
    {
        return $this->players_repository
                    ->get();
    }


    /**
     * レコード保存
     *
     * @param array $data
     * @return void
     */
    public function insertPlayersRecord(array $data): void
    {
      // まとめて保存したいのでinsertを使用
      DB::table('players')->insert($data);
    }


    /**
     * 名前で検索
     *
     * @param string|null $name_jp
     * @param string|null $name_en
     * @return Collection
     */
    public function searchPlayerByName(?string $name_jp, ?string $name_en): Collection
    {
        return $this->players_repository
                    ->where('name_en', '=', $name_en)
                    ->where('name_jp', '=', $name_jp)
                    ->get();
    }
}