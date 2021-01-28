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


/* APIのルート */
Route::middleware('throttle:60,1', 'auth:web')->prefix('/api/v1')->group(function () {
    Route::get('/brands', 'FavoriteBrandController@fetchBrands');
    Route::post('/brands/create', 'FavoriteBrandController@addBrand');
    Route::delete('/brands/delete', 'FavoriteBrandController@deleteBrand');

    Route::get('/rankings', 'RankingController@fetchRankings');
    Route::get('/players_news', 'NewsController@fetchPlayersNews');
    Route::get('/brands_news', 'NewsController@fetchBrandsNews');
    Route::get('/player_movies', 'MovieController@fetchPlayerMovies');
    Route::get('/brand_movies', 'MovieController@fetchBrandMovies');
    Route::get('/tour_schedules', 'TourScheduleController@fetchTourSchedules');

    Route::get('/players', 'FavoritePlayerController@fetchPlayers');
    Route::post('/add_player', 'FavoritePlayerController@addPlayer');
    Route::delete('/delete_player', 'FavoritePlayerController@deletePlayer');
    Route::get('/search_players', 'FavoritePlayerController@searchPlayers');
    Route::get('/analysis_age', 'AnalysisController@fetchAgeAnalysis');
});