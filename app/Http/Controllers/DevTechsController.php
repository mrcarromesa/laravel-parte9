<?php

namespace App\Http\Controllers;

use App\Http\Models\Devs;
use App\Http\Models\Techs;
use Illuminate\Http\Request;

class DevTechsController extends Controller
{

    /**
     * Retorna os devs em que as techs dominadas vinculadas a ele
     * estejam com o status = a @param string $status
     */
    private function getDevsTechsByStatus($status)
    {
        // use ($status) : envia a variavel $status presente na funcao para a
        // funcao aninhada
        $devs = Devs::whereHas('techs', function ($query) use ($status) {
            return $query->where('status', $status);
        })->get();

        return $devs;
    }

    /**
     * Retorna os devs em que as techs dominadas vinculadas a ele
     * estejam com o status diferente a @param string $status
     */
    private function getDevsTechsByNotStatus($status)
    {
        $devs = Devs::whereDoesntHave('techs', function ($query) use ($status) {
            return $query->where('status', $status);
        })->get();

        return $devs;
    }

    /**
     * Retorna as techs dominada pelo dev informado, conforme o status tambem enviado
     * @param Devs $dev; Esperado: $dev = Devs::find(1);
     * @param string $status
     */
    private function getComplexDevTechsByDev(Devs $dev, $status)
    {
        return $dev->belongsToMany('App\Http\Models\Techs', 'dev_techs', 'id_dev', 'id_tech')
            ->withPivot('id', 'status')->wherePivot('status', $status)->get();
    }

    /**
     * Retorna as techs dominada pelo dev informado, conforme o status tambem enviado
     * @param Devs $dev; Esperado: $dev = Devs::find(1);
     * @param string $status
     */
    private function getDevTechsByDev(Devs $dev, $status)
    {
        return $dev->techs()->wherePivot('status', $status)->get();
    }

    /**
     * Retorna os devs e as techs dominada por ele
     */
    private function getDevsWithTechs()
    {
        $devs = Devs::with(['techs'])->get();
        return $devs;
    }


    /**
     * Retorna os devs e as techs dominada por ele filtrado
     */
    private function getDevsWithTechsFilter($status)
    {
        $devs = Devs::whereHas('techs', function ($query) use ($status) {
            return $query->where('status', $status);
        })->with(['techs' => function ($query) use ($status) {
            return $query->wherePivot('status', $status);
        }])->get();
        return $devs;
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id = request('id');
        //return $this->getDevsTechsByStatus('A');
        //return $this->getDevsTechsByNotStatus('A');
        //return $this->getComplexDevTechsByDev(Devs::find($id), 'A');
        //return $this->getDevTechsByDev(Devs::find($id), 'A');
        return $this->getDevsWithTechs();
        //return $this->getDevsWithTechsFilter('A');
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
        $json = $request->only(['nome', 'github_username']);
        $json_techs = $request->only(['techs']);

        // Nao obrigatorio apenas um exemplo de consulta de techs pelo id.
        $techs = Techs::whereIn('id', $json_techs['techs'])->get();
        $devs = Devs::create($json);

        $devs->techs()->attach($techs, ['status' => 'A']);
        $devs->save();

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
        //$techs = Techs::whereIn('id', [1])->get();
        $json_techs = $request->only(['techs']);
        //dd($json_techs['techs']);
        $devs = Devs::find($id);
        //$devs->techs()->syncWithoutDetaching([4 => ['status' => 'B']]);
        $devs->techs()->syncWithoutDetaching($json_techs['techs']);
        $devs->save();
        return $devs;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $json_techs = request()->only(['techs']);
        //$techs = Techs::whereIn('id', [1])->get();

        //$devs = Devs::with(['devTechs.techs'])->get();
        $devs = Devs::find($id);
        $devs->techs()->detach($json_techs['techs']);
        $devs->save();

        return response()->json(['ok', 'Techs removidas.']);
    }
}
