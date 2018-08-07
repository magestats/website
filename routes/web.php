<?php
Route::get('/', '\App\Http\Controllers\WelcomeController@index')->name('welcome');
Route::get('/projects/{user}/{repo}', '\App\Http\Controllers\ProjectsController@index')->name('projects');
Route::get('/contributors', '\App\Http\Controllers\ContributorsController@index')->name('contributors');
Route::get('/about', function () {
    return view('about')->with('title', 'About Magestats');
});