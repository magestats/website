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

Route::get('/', function () {
    return view('welcome');
});

Route::get('contributions/{user}/{repo}', '\App\Http\Controllers\Repository\ContributionsController@index')->name('contributions');
Route::get('statistics/{user}/{repo}', '\App\Http\Controllers\Repository\StatisticsController@index')->name('contributions');
Route::get('participations/{user}/{repo}', '\App\Http\Controllers\Repository\ParticipationsController@index')->name('participations');
Route::get('pullrequests/closed/{user}/{repo}', '\App\Http\Controllers\PullRequest\ClosedController@index')->name('closed');
Route::get('pullrequests/open/{user}/{repo}', '\App\Http\Controllers\PullRequest\OpenController@index')->name('closed');