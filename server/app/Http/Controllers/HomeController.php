<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * 新しいデザインのホーム画面
     */
    public function index()
    {
        $user_id = Auth::user()->id;
        return view('home.index', compact('user_id'));
    }
}
