@extends('layouts.layout')

@section('content')
  @if(Auth::check())

    <div class="favorite-player-wrapper container pt-140">

      <div class="pt-3">
        <div class="favorite-contents-left">
          <div class="text-white bg-dark favorite-head text-center h4 font-alegreya">Brand Lists</div>
          <table class="table m-0">
            <thead class="thead-dark">
              <th class="favorite-name-jp-w text-center">Brand Name</th>
              <th class="favorite-country-w text-center">country</th>
              <th class="favorite-age-w text-center">add</th>
            </thead>
          </table>
          <div class="favorite-tbody">
            <table class="table table-striped">
              <tbody>

              </tbody>
            </table>
          </div>
        </div>
      <div>
    </div>
  @else
    @include('top.welcome')
  @endif
@endsection


