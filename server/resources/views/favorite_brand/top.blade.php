@extends('common.layout')

@section('title', 'Favorite')

@section('content')
    @if(Auth::check())
        <div id="favorite-brand-vue">
            <favorite-brand-component v-bind:user_id="{{ ($user_id) }}"></favorite-brand-component>
        </div>
    @else
        @include('top.welcome')
    @endif
@endsection