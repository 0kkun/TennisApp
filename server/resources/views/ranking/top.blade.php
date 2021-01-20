@extends('common.layout')

@section('title', 'Ranking')

@section('content')
    @if(Auth::check())
        <div id="ranking-vue">
            <ranking-component v-bind:user_id="{{ ($user_id) }}"></ranking-component>
        </div>
    @endif
@endsection