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

Route::get('devs', function () {
    $devs = Devs::all();
    return $devs;
});

Route::put('devs/{id}', function ($id) {

    // obtendo os dados enviados via metodo PUT
    $json = request()->only(['nome', 'github_username']);

    // buscar os dados na tabela pelo id enviado
    $devs = Devs::find($id);

    // verifica se o registro com o id informado acima existe
    if (!$devs) {
        // caso não exista RETORNA um erro 404
        return response()->json(['error' => 'Not found'], 404);
    }
    // caso exista...

    // realiza o update do registro
    $devs->update($json);
    $devs->save();

    // retorna o registro alterado
    return response()->json($devs);
    //return $devs;
});

Route::delete('devs/{id}', function ($id) {

    // buscar os dados na tabela pelo id enviado
    $devs = Devs::find($id);

    // verifica se o registro com o id informado acima existe
    if (!$devs) {
        // caso não exista RETORNA um erro 404
        return response()->json(['error' => 'Not found'], 404);
    }
    // caso exista...

    // realiza o delete
    $devs->delete();

    // Retorna uma mensagem
    return response()->json(['ok' => 'Registro removido']);
});
