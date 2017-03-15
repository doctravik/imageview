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

Route::get('/home', 'HomeController@index')->name('home.index');

Route::post('/photo', 'PhotoController@store')->name('photo.store');
Route::get('/photo/{photo}', 'PhotoController@show')->name('photo.show');

Auth::routes();