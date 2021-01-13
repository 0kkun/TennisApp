@extends('common.layout')

@section('title', 'Ranking')

@section('content')
    @if(Auth::check())
        <div id="ranking-vue">
            <ranking-component></ranking-component>
        </div>
    @else
        @include('top.welcome')
    @endif
@endsection