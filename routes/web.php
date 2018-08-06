<?php
Route::get('/', '\App\Http\Controllers\WelcomeController@index')->name('welcome');
Route::get('contributions/{user}/{repo}', '\App\Http\Controllers\Repository\ContributionsController@index')->name('contributions');
Route::get('statistics/{user}/{repo}', '\App\Http\Controllers\Repository\StatisticsController@index')->name('contributions');
Route::get('participations/{user}/{repo}', '\App\Http\Controllers\Repository\ParticipationsController@index')->name('participations');
Route::get('pullrequests/closed/{user}/{repo}', '\App\Http\Controllers\PullRequest\ClosedController@index')->name('closed');
Route::get('pullrequests/open/{user}/{repo}', '\App\Http\Controllers\PullRequest\OpenController@index')->name('closed');