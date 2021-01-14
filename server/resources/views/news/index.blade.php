@extends('common.layout')

@section('title', 'News')

@section('content')
    @if(Auth::check())
        <div id="news-vue">
            <news-component></news-component>
        </div>
    @else
        @include('top.welcome')
    @endif
@endsection