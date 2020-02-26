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
    return view('pages.welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/profile/{id}', 'ProfileController@index')->name('profile.show');

Route::post('profile', 'ProfileController@store')->name('profiles.store');

Route::post('home/access_token', 'HomeController@saveAccessToken')->name('home.access_token');

Route::post('profile/data', 'ProfileController@getProfileData')->name('profiles.getProfileData');

Route::put('profile', 'ProfileController@update')->name('profiles.update');
