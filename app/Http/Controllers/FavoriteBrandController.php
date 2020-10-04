<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\BrandsRepository;

class FavoriteBrandController extends Controller
{
    private $brands_repository;

        /**
     * リポジトリをDI
     * 
     * @param PlayersRepository $players_repository
     */
    public function __construct(
        BrandsRepository $brands_repository
    )
    {
        $this->brands_repository = $brands_repository;
    }

    public function index()
    {
        $brands = $this->brands_repository->getAll()->toArray();

        return view('favorite_brand.index',compact('brands'));
    }
}
