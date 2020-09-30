<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('/', 'TopController@index')->name('top.index');

Route::get('/favorite_player', 'FavoritePlayerController@index')->name('favorite_player.index');
Route::post('/favorite_player/add', 'FavoritePlayerController@add')->name('favorite_player.add');
Route::post('/favorite_player/remove', 'FavoritePlayerController@remove')->name('favorite_player.remove');

Route::get('/favorite_brand', 'FavoriteBrandController@index')->name('favorite_brand.index');

Auth::routes();
