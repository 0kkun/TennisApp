@extends('common.layout')

@section('title', 'Home')

@section('content')
    @if(Auth::check())
        <div id="home-vue">
            <tour-schedule-component v-bind:user_id="{{ ($user_id) }}"></tour-schedule-component>
            <player-movie-component v-bind:user_id="{{ ($user_id) }}"></player-movie-component>
            <brand-movie-component v-bind:user_id="{{ ($user_id) }}"></brand-movie-component>
        </div>
    @endif
@endsection