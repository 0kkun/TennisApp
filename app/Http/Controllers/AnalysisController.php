<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    private $brands_repository;

    /**
     * リポジトリをDI
     * 
     */
    public function __construct(

    )
    {

    }


    public function index()
    {
      return view('analysis.index');
    }
}
