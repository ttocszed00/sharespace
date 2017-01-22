<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::get('/key/{key?}', function ($key = null) {
    $return = [
        'Name'         => 'ShareSpace',
        'RequestType'  => 'POST',
        'RequestUrl'   => URL::to('/upload'),
        'FileFormName' => 'file',
        'ResponeType'  => 'Text',
        'URL'          => '$json:href$',
        'DeletionURL'  => '$json:delete$'
    ];

    if ($key) $return['Arguments'] = ['key' => $key];

    return $return;
});
