<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\PlayersRepository;

class FavoritePlayerController extends Controller
{
    private $players_repository;

    /**
     * リポジトリをDI
     * 
     * @param PlayersRepository $players_repositor
     */
    public function __construct(
        PlayersRepository $players_repository
    )
    {
        $this->players_repository = $players_repository;
    }


    public function index()
    {
        $players = $this->players_repository->getAll();
        $players->toArray();

        return view('favorite_player.index', compact('players'));
    }
}
