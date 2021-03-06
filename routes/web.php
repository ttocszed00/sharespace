<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index');


Route::post('/upload', 'FileController@postUpload');
Route::get('/{user}/{file}', 'FileController@getFile');
Route::get('/{user}/thumbs/{file}', 'FileController@getThumb');
Route::get('d/','FileController@deleteImage');
Route::get('/test', 'TestController@test');


