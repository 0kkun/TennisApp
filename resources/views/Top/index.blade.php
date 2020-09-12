@extends('layouts.layout')

@section('content')
  @if(Auth::check())
    <div class="top-wrapper container-fluid pt-140">
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
  @else
    @include('top.welcome')
  @endif
@endsection