<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });



Route::prefix('v1')->group(function () {
    Route::get('/brands', 'FavoriteBrandController@fetchBrands');
    Route::post('/brands/create', 'FavoriteBrandController@addBrand');
    Route::delete('/brands/delete', 'FavoriteBrandController@deleteBrand');

    Route::get('/rankings', 'RankingController@fetchRankings');
    Route::get('/players_news', 'NewsController@fetchPlayersNews');
    Route::get('/brands_news', 'NewsController@fetchBrandsNews');
    Route::get('/movies', 'MovieController@fetchMovies');
    Route::get('/tour_schedules', 'TourScheduleController@fetchTourSchedules');

    Route::get('/players', 'FavoritePlayerController@fetchPlayers');
    Route::post('/add_player', 'FavoritePlayerController@addPlayer');
    Route::delete('/delete_player', 'FavoritePlayerController@deletePlayer');
    Route::get('/search_players', 'FavoritePlayerController@searchPlayers');
});