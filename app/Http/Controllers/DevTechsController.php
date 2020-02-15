<?php

namespace App\Http\Controllers;

use App\Http\Models\Devs;
use App\Http\Models\Techs;
use Illuminate\Http\Request;

class DevTechsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $devs = Devs::with(['techs'])->get();
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
        $techs = Techs::whereIn('id', [1, 4])->get();
        $devs = Devs::create(['nome' => 'N1', 'github_username' => 'n1']);
        //$devs->techs()->attach($techs, ['status' => 'A']);
        $devs->techs()->attach($techs);
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
        $techs = Techs::whereIn('id', [1])->get();

        //$devs = Devs::with(['devTechs.techs'])->get();
        $devs = Devs::find(7);
        $devs->techs()->syncWithoutDetaching([4 => ['status' => 'B']]);
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
        $techs = Techs::whereIn('id', [1])->get();

        //$devs = Devs::with(['devTechs.techs'])->get();
        $devs = Devs::find(7);
        $devs->techs()->detach($techs);
        $devs->save();
    }
}
