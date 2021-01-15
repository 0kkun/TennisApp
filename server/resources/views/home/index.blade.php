@extends('common.layout')

@section('title', 'Favorite')

@section('content')
    @if(Auth::check())
        <div id="home-vue">
            <tour-component v-bind:user_id="{{ ($user_id) }}"></tour-component>
            <movie-component v-bind:user_id="{{ ($user_id) }}"></movie-component>
        </div>
    @else
        @include('top.welcome')
    @endif
@endsection