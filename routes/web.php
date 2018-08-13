<?php
Route::get('/', function () {return view('landingpage');});
Route::get('/start', '\App\Http\Controllers\WelcomeController@index')->name('welcome');
Route::get('/repositories/{user}/{repo}', '\App\Http\Controllers\RepositoriesController@index')->name('repositories');
Route::get('/contributors', '\App\Http\Controllers\ContributorsController@index')->name('contributors');
Route::get('/about', function () {return view('about')->with('title', 'About');});
