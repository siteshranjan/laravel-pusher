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
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::post('/home', 'HomeController@index')->name('home');
Route::post('/searchByKey', 'HomeController@searchByKey');
Route::post('/acceptFriend', 'HomeController@acceptFriend');
Route::post('/declinedRequest', 'HomeController@declinedRequest');
Route::get('/user-profile/{id}', 'HomeController@userProfile');
Route::post('/addFriendToTheList', 'HomeController@addFriendToTheList');
