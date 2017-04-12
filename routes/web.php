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

Route::get('/', 'WelcomeController@index')->name('welcome.index');

Route::get('/photos/{photo}', 'PhotoController@show')->name('photos.show');

Route::get('/albums/{album}', 'AlbumController@show')->name('albums.show');

Route::get('/home', 'Admin\AlbumController@index')->name('home');

Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function() {
    Route::get('/albums', 'AlbumController@index')->name('admin.album.index');
    Route::get('/albums/{album}', 'AlbumController@show')->name('admin.albums.show');
    Route::post('/albums', 'AlbumController@store')->name('admin.album.store');
    Route::delete('/albums/{album}', 'AlbumController@destroy')->name('admin.album.destroy');
    
    Route::post('/albums/{album}/photos', 'PhotoController@store')->name('albums.photos.store');
});

Route::group(['prefix' => 'webapi', 'namespace' => 'Webapi'], function() {
    Route::get('/albums/{album}/photos', 'PhotoController@index');
    Route::post('/albums/{album}/photos', 'PhotoController@store');
    Route::delete('/albums/{album}/photos/{photo}', 'PhotoController@destroy');
});

Auth::routes();