@extends('common.layout')

@section('title', 'Brand')

@section('content')
    @if(Auth::check())
        <div id="favorite-brand-vue">
            <favorite-brand-component v-bind:user_id="{{ ($user_id) }}"></favorite-brand-component>
        </div>
    @endif
@endsection