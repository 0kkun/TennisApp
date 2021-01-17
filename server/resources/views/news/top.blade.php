@extends('common.layout')

@section('title', 'News')

@section('content')
    @if(Auth::check())
        <div id="news-vue">
            <players-news-component v-bind:user_id="{{ ($user_id) }}"></players-news-component>
        </div>
    @else
        @include('top.welcome')
    @endif
@endsection