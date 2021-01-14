@extends('common.layout')

@section('title', 'News')

@section('content')
    @if(Auth::check())
        <div id="news-vue">
            <news-component v-bind:user_id="{{ ($user_id) }}"></news-component>
        </div>
    @else
        @include('top.welcome')
    @endif
@endsection