<?php

namespace App\Http\Controllers;

use App\Http\Models\Devs;
use Illuminate\Http\Request;

class DevPostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dentro do with estamos utilizando o nome da função que foi criada no Model Devs, a qual retorna os posts relacionados a cada dev que será consultado
        $dev = Devs::select()->with(['posts'])->get();
        return $dev;
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
        // Separa o que irá compor o valor do dev
        $json = $request->only(['github_username', 'nome']);
        // Separa o que irá compor o valor do post
        $json_post = $request->only(['post']);

        // insere registro na tabela dev
        $dev = Devs::create($json);

        // insere registro na tabela post com o dev_id criado pelo comando acima.
        $dev->posts()->create($json_post['post']);

        $dev->save();

        return $dev;
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
        $json = $request->only(['nome', 'github_username']);
        $json_post = $request->only(['post']);

        $dev = Devs::find($id);

        if (!$dev) {
            return response()->json(['error' => 'Not Found'], 404);
        }

        $dev->posts()->create($json_post['post']);

        $dev->update($json);
        $dev->save();


        return $dev;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $dev = Devs::find($id);

        if (!$dev) {
            return response()->json(['error' => 'Not Found'], 404);
        }

        $dev->posts()->delete();

        $dev->delete();

        return response()->json(['ok' => 'Registro removido']);
    }
}
