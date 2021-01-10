<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface PlayersRepository
{
    /**
     * 全レコードを取得
     *
     * @return Collection
     */  
    public function getAll(): Collection;


    /**
     * バルクインサート
     * テーブルに保存・更新する
     * 
     * @param array $data
     * @return void
     */
    public function bulkInsertOrUpdate($data): void;


    /**
     * 名前で検索
     *
     * @param array $inputs
     * @return Collection
     */
    public function searchPlayers( array $inputs ): Collection;

    /**
     * 全ての国名を取得
     *
     * @return array
     */
    public function getAllCountryNames(): array;


    /**
     * youtube_active = 1のレコードのみ返す
     *
     * @return Collection
     */
    public function getActivePlayers(): Collection;
}