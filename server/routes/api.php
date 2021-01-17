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

Route::get('/get_brands_data', 'ApiController@getBrandsData');
Route::post('/add_brand', 'ApiController@addBrand');
Route::delete('/delete_brand', 'ApiController@deleteBrand');


Route::get('/v1/rankings', 'RankingController@fetchRankings');
Route::get('/v1/news', 'NewsController@fetchNews');
Route::get('/v1/movies', 'MovieController@fetchMovies');
Route::get('/v1/tour_schedules', 'TourScheduleController@fetchTourSchedules');
Route::get('/v1/players', 'FavoritePlayerController@fetchPlayers');
Route::post('/v1/add_player', 'FavoritePlayerController@addPlayer');
Route::delete('/v1/delete_player', 'FavoritePlayerController@deletePlayer');
Route::get('/v1/search_players', 'FavoritePlayerController@searchPlayers');