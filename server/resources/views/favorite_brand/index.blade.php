@extends('layouts.layout')

@section('content')
    @if(Auth::check())
        <div class="container" style="padding-top:197px;" id="favorite-brand-vue">
            <favorite-brand-component v-bind:user_id="{{ ($user_id) }}"></favorite-brand-component>
        </div>
    @else
        @include('top.welcome')
    @endif
@endsection


