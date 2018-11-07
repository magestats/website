<?php
Route::get('/', '\App\Http\Controllers\WelcomeController@index')->name('welcome');
Route::get('/repositories/{user}/{repo}/{year?}/{month?}', '\App\Http\Controllers\RepositoriesController@index')->name('repositories');
Route::get('/reports/{year?}/{month?}', '\App\Http\Controllers\ReportsController@index')->name('reports');
Route::get('/contributors/{year?}/{month?}', '\App\Http\Controllers\ContributorsController@index')->name('contributors');
Route::get('/contributor/{name?}', '\App\Http\Controllers\ContributorsController@byUsername')->name('contributor');
Route::get('/sitemap', '\App\Http\Controllers\SitemapController@index')->name('sitemap');
Route::get('/about', function () {return view('about')->with('title', 'About');});

