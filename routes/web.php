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
})->name('/');

Route::get('login/spotify', 'Auth\LoginController@redirectToProvider')
    ->name('login');
Route::get('login/spotify/callback', 'Auth\LoginController@handleProviderCallback');
Route::get('/logout', 'Auth\LoginController@logout')
    ->name('logout');
Route::get('/docs', function () {
    return view('docs');
});

Route::resource('exporter', 'ExporterController')
    ->middleware('web', 'auth');
Route::get('/export/{file}', 'ExporterController@file_download');
Route::get('/datatables/exports', 'ExporterController@make_exports')
    ->name('datatables.exports')
    ->middleware('web', 'auth');
Route::get('/user/profile', 'UserController@index')
    ->middleware('web', 'auth');
Route::resource('search', 'SearchController')
    ->middleware('web', 'auth');
