@extends('layouts.layout')

@section('content')
      <!-- トップリンクバー -->
      <div class="container-fluid top-link">
        <div class="row text-center bg-dark">
          <!-- <div class="col-4 pt-2 pb-2"><a class="text-white h5 font-alegreya" href="#">News</a></div>
          <div class="col-4 pt-2 pb-2"><a class="text-white h5 font-alegreya" href="#">Movie</a></div>
          <div class="col-4 pt-2 pb-2"><a class="text-white h5 font-alegreya" href="#">Rank & Schedule</a></div> -->
        </div>
      </div>

      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-4 pt-3">
            @include('top.contents-left')
          </div>
          <div class="col-sm-4 pt-3">
            @include('top.contents-center')
          </div>
          <div class="col-sm-4 pt-3">
            @include('top.contents-right')
          </div>
        </div>
      </div>
@endsection