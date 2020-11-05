<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Repositories\Contracts\FavoritePlayersRepository;
use Illuminate\Support\Facades\Auth;

class AxiosTestController extends Controller
{


    private $favorite_players_repository;


    /**
     * リポジトリをDI
     * 
     * @param PlayersRepository $players_repository
     */
    public function __construct(
      FavoritePlayersRepository $favorite_players_repository
    )
    {
        $this->favorite_players_repository = $favorite_players_repository;
    }


    public function index() {
      return view('axios_test.index');
    }

    public function getPlayers()
    {
      $players = Player::all();
      return $players;
    }

    public function addFavoritePlayer(Request $request)
    {
      $data['player_id'] = $request->input('id');

      // バルクインサートで保存
      if ( !empty($data) ) {
          $this->favorite_players_repository->bulkInsertOrUpdate( $data );
      }
      $players = Player::all();
      return $players;
  }
}
