<?php

namespace App\Services\FavoritePlayer;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Repositories\Contracts\PlayersRepository;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class FavoritePlayerService implements FavoritePlayerServiceInterface
{
    private $players_repository;

    /**
     * FavoritePlayerController constructor.
     * @param PlayersRepository $players_repository
     */
    public function __construct(
      PlayersRepository $players_repository
    )
    {
        $this->players_repository = $players_repository;
    }

    public function searchPlayers(array $inputs): Collection
    {
        $players = $this->players_repository->searchPlayers( $inputs );

        return $players;
    }

}