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
            $user_id = Auth::id();
            return view('home.index', compact('user_id'));
        } else {
            return view('top.index');
        }
    }
}
