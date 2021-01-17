<?php

Auth::routes();

Route::get('/', 'TopController@index')->name('top.index');

Route::prefix('favorite_player')->group(function () {
    Route::get('/index', 'FavoritePlayerController@index')->name('favorite_player.index');
    Route::post('/add', 'FavoritePlayerController@add')->name('favorite_player.add');
    Route::post('/remove', 'FavoritePlayerController@remove')->name('favorite_player.remove');
});

Route::prefix('favorite_brand')->group(function () {
    Route::get('/index', 'FavoriteBrandController@index')->name('favorite_brand.index');
    Route::post('/add', 'FavoriteBrandController@add')->name('favorite_brand.add');
    Route::post('/remove', 'FavoriteBrandController@remove')->name('favorite_brand.remove');
});

Route::get('/analysis', 'AnalysisController@index')->name('analysis.index');

Route::get('/favorite_brand', 'FavoriteBrandController@top')->name('favorite_brand.top');
Route::get('/favorite_player', 'FavoritePlayerController@top')->name('favorite_player.top');
Route::get('/ranking', 'RankingController@top')->name('ranking.top');
Route::get('/news', 'NewsController@top')->name('news.top');
Route::get('/home', 'HomeController@index')->name('home.index');