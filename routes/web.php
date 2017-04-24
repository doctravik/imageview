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
Route::get('/home', 'Admin\AlbumController@index')->name('home')->middleware('admin');

// Route::get('/photos/{photo}', 'PhotoController@show')->name('photos.show');

// Route::get('/albums/{album}', 'AlbumController@show')->name('albums.show');


Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => 'admin'], function() {
    Route::get('/albums', 'AlbumController@index')->name('admin.album.index');
    Route::get('/albums/{album}', 'AlbumController@show')->name('admin.albums.show');
    Route::post('/albums', 'AlbumController@store')->name('admin.album.store');
    Route::delete('/albums/{album}', 'AlbumController@destroy')->name('admin.album.destroy');
    
    Route::post('/albums/{album}/photos', 'PhotoController@store')->name('albums.photos.store');
});

Route::group(['prefix' => 'webapi', 'namespace' => 'Webapi',  'middleware' => 'admin'], function() {
    Route::get('/albums/{album}/photos', 'PhotoController@index');
    Route::post('/albums/{album}/photos', 'PhotoController@store');
    Route::patch('/albums/{album}/photos/sorting', 'SortPhotos')->name('photos.sort');
    
    Route::patch('/photos/{photo}', 'PhotoController@update');
    Route::delete('/photos/{photo}', 'PhotoController@destroy');

    Route::patch('/photos/{photo}/avatars', 'UpdateAlbumAvatar');
});

Route::get('/webapi/albums', 'Webapi\AlbumController@index');

Route::get('/account/confirm', 'AccountController@confirm')->name('account.confirm');
Route::get('/account/activate/{token}', 'Auth\ActivateToken')->name('account.activate');
Route::post('/activation/token/resend/{user}', 'Auth\ResendActivationToken')->name('activation.token.resend');

Auth::routes();