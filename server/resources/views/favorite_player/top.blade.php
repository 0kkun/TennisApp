@extends('common.layout')

@section('title', 'Player')

@section('content')
    @if(Auth::check())
        <div id="favorite-player-vue">
            <favorite-player-component v-bind:user_id="{{ ($user_id) }}"></favorite-player-component>
        </div>
    @endif
@endsection