<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class TopController extends Controller
{
    /**
     * トップページ遷移
     */
    public function index()
    {
        if ( Auth::check()) {
            return view('home.index');
        } else {
            return view('top.index');
        }
    }
}
