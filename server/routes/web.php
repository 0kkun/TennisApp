<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Auth::routes();

/* トップページ */
Route::get('/', 'TopController@index')->name('top.index');

Route::middleware('auth:web')->group(function () {
    Route::get('/home', 'HomeController@index')->name('home.index');
    Route::get('/news', 'NewsController@top')->name('news.top');
    Route::get('/ranking', 'RankingController@top')->name('ranking.top');
    Route::get('/favorite_brand', 'FavoriteBrandController@top')->name('favorite_brand.top');
    Route::get('/favorite_player', 'FavoritePlayerController@top')->name('favorite_player.top');
});

/* APIのルート */
Route::middleware('throttle:60,1', 'auth:web')->prefix('/api/v1')->group(function () {

    Route::prefix('/brands')->name('brands.')->group(function () {
        Route::get('/', 'FavoriteBrandController@fetchBrands')->name('fetch');
        Route::post('/add', 'FavoriteBrandController@addBrand')->name('add');
        Route::delete('/delete', 'FavoriteBrandController@deleteBrand')->name('delete');
    });

    Route::prefix('/players')->name('players.')->group(function () {
        Route::get('/', 'FavoritePlayerController@fetchPlayers')->name('fetch');
        Route::post('/add', 'FavoritePlayerController@addPlayer')->name('add');
        Route::delete('/delete', 'FavoritePlayerController@deletePlayer')->name('delete');
        Route::get('/search', 'FavoritePlayerController@searchPlayers')->name('search');
    });

    Route::get('/rankings', 'RankingController@fetchRankings');

    Route::get('/players_news', 'NewsController@fetchPlayersNews');
    Route::get('/brands_news', 'NewsController@fetchBrandsNews');

    Route::get('/player_movies', 'MovieController@fetchPlayerMovies');
    Route::get('/brand_movies', 'MovieController@fetchBrandMovies');

    Route::get('/tour_schedules', 'TourScheduleController@fetchTourSchedules');

    Route::get('/analysis_age', 'AnalysisController@fetchAgeAnalysis');
});

/* 以下ルートは使用停止中 */
// Route::prefix('favorite_player')->group(function () {
//     Route::post('/add', 'FavoritePlayerController@add')->name('favorite_player.add');
//     Route::post('/remove', 'FavoritePlayerController@remove')->name('favorite_player.remove');
// });

// Route::prefix('favorite_brand')->group(function () {
//     Route::post('/add', 'FavoriteBrandController@add')->name('favorite_brand.add');
//     Route::post('/remove', 'FavoriteBrandController@remove')->name('favorite_brand.remove');
// });

// Route::middleware('auth:web')->group(function () {
//     Route::get('favorite_player/index', 'FavoritePlayerController@index')->name('favorite_player.index');
//     Route::get('favorite_brand/index', 'FavoriteBrandController@index')->name('favorite_brand.index');
//     Route::get('/analysis', 'AnalysisController@index')->name('analysis.index');
// });