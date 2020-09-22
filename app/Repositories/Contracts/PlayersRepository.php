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
     * @var string $name
     * @return Collection
     */
    public function searchPlayerByName(?string $name_jp, ?string $name_en): Collection;
}