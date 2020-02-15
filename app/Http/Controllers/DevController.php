<?php

namespace App\Http\Controllers;

use App\Http\Models\Devs;
use Illuminate\Http\Request;

class DevController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $devs = Devs::all();
        return $devs;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // obtem os dados enviados via post
        $json = $request->json()->all();

        // insere o registro na base
        $devs = Devs::create($json);
        $devs->save();

        // retorna o registro
        return $devs;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // obtendo os dados enviados via metodo PUT
        $json = $request->only(['nome', 'github_username']);

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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
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
    }
}
