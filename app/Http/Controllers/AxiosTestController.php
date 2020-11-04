<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;

class AxiosTestController extends Controller
{
    public function index() {
      return view('axios_test.index');
    }

    public function getPlayers()
    {
      $players = Player::all();
      return $players;
    }
}
