<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('reset-league', 'HomeController@postResetLeague');
Route::get('standings', 'HomeController@getStandings');
Route::get('results/{week}', 'HomeController@getResults');
Route::post('play-all', 'HomeController@postPlayAll');
Route::post('next-week', 'HomeController@postNextWeek');
Route::post('get-champion/{week}', 'HomeController@getChampionPercentage');