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

Route::prefix('favorite_player')->group(function () {
  Route::get('/', 'FavoritePlayerController@index')->name('favorite_player.index');
  Route::post('/add', 'FavoritePlayerController@add')->name('favorite_player.add');
  Route::post('/remove', 'FavoritePlayerController@remove')->name('favorite_player.remove');
});

Route::prefix('favorite_brand')->group(function () {
  Route::get('/', 'FavoriteBrandController@index')->name('favorite_brand.index');
  Route::post('/add', 'FavoriteBrandController@add')->name('favorite_brand.add');
  Route::post('/remove', 'FavoriteBrandController@remove')->name('favorite_brand.remove');
});

Route::get('/analysis', 'AnalysisController@index')->name('analysis.index');

Route::get('/axios_test', 'AxiosTestController@index')->name('axios_test.index');

Auth::routes();
