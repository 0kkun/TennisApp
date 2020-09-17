<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\PlayersRepository;

class TopController extends Controller
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

    /**
     * トップページ遷移
     *
     * @return void
     */
    public function index()
    {
        $players = $this->players_repository->getAll();

        return view('top.index', compact('players'));
    }
}
