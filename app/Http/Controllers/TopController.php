<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\PlayersRepository;

class TopController extends Controller
{
    /**
     * トップページ遷移
     *
     * @return void
     */
    public function index()
    {
        return view('top.index');
    }
}
