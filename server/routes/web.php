<?php

Auth::routes();

Route::prefix('favorite_player')->group(function () {
    Route::post('/add', 'FavoritePlayerController@add')->name('favorite_player.add');
    Route::post('/remove', 'FavoritePlayerController@remove')->name('favorite_player.remove');
});

Route::prefix('favorite_brand')->group(function () {
    Route::post('/add', 'FavoriteBrandController@add')->name('favorite_brand.add');
    Route::post('/remove', 'FavoriteBrandController@remove')->name('favorite_brand.remove');
});

Route::get('/', 'TopController@index')->name('top.index');

Route::middleware('auth:web')->group(function () {
    Route::get('favorite_player/index', 'FavoritePlayerController@index')->name('favorite_player.index');
    Route::get('favorite_brand/index', 'FavoriteBrandController@index')->name('favorite_brand.index');
    Route::get('/analysis', 'AnalysisController@index')->name('analysis.index');

    Route::get('/home', 'HomeController@index')->name('home.index');
    Route::get('/news', 'NewsController@top')->name('news.top');
    Route::get('/ranking', 'RankingController@top')->name('ranking.top');
    Route::get('/favorite_brand', 'FavoriteBrandController@top')->name('favorite_brand.top');
    Route::get('/favorite_player', 'FavoritePlayerController@top')->name('favorite_player.top');
});