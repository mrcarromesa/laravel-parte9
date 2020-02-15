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

use App\Http\Models\Devs;

Route::get('/', function () {
    return view('welcome');
});

Route::post('devs', function () {
    $json = request()->json()->all();
    $devs = Devs::create($json);
    $devs->save();

    return $devs;
});
