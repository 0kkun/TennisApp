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
     * レコード保存
     *
     * @param array $data
     * @return void
     */
    public function insertPlayersRecord(array $data): void;

    /**
     * 名前で検索
     *
     * @var string $name
     * @return Collection
     */
    public function searchPlayerByName(?string $name_jp, ?string $name_en): Collection;
}