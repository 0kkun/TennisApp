<?php

namespace App\Services\FavoritePlayer;

use Illuminate\Support\Collection;

interface FavoritePlayerServiceInterface
{

    /**
     * 選手検索
     *
     * @param array $inputs
     * @return Collection
     */
    public function searchPlayers( array $inputs ): Collection;
}