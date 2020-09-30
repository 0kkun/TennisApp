<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoriteBrandController extends Controller
{
    public function index()
    {
        return view('favorite_brand.index');
    }
}
