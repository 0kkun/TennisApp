<?php

namespace App\Services\FavoritePlayer;

use Illuminate\Support\Collection;

interface FavoritePlayerServiceInterface
{

  public function searchPlayers( array $inputs ): Collection;
}