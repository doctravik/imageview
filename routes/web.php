<?php

Route::get('/about', function() {
    return view('about');
})->name('about');

Route::get('/', 'WelcomeController@index')->name('welcome.index');
Route::get('/home', 'Admin\AlbumController@index')->name('home')->middleware('admin');

Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => 'admin'], function() {
    Route::get('/albums', 'AlbumController@index');
    Route::post('/albums', 'AlbumController@store')->name('admin.album.store');
    Route::get('/albums/{album}', 'AlbumController@show')->name('admin.album.show');
    Route::get('/albums/{album}/edit', 'AlbumController@edit')->name('admin.album.edit');
    Route::patch('/albums/{album}', 'AlbumController@update')->name('admin.album.update');
    Route::delete('/albums/{slug}', 'AlbumController@destroy')->name('admin.album.destroy');
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
