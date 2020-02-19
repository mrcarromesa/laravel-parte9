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

/*
o primeiro parametro de resource 'dev' precisa ser o mesmo
que o primeiro parametro de 'parameters()'
e o segundo parametro de parameters, preferenciamente poderá ser 'id'
*/

Route::resource('dev', 'DevController')->parameters([
    'dev' => 'id'
]);

Route::resource('post', 'PostController')->parameters([
    'post' => 'id'
]);

Route::resource('dev-post', 'DevPostController')->parameters([
    'dev-post' => 'id'
]);

Route::resource('dev-tech', 'DevTechsController')->parameters([
    'dev-tech' => 'id'
]);

Route::group(['middleware' => 'auth:api'], function () {
    //Route::post('details', 'API\UserController@details');
});
