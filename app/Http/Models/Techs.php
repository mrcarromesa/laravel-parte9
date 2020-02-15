<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Techs extends Model
{
    protected $table = 'techs';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function devs()
    {

        // Parametros:
        // 1 - Classe do outro model principal, nao o intermediario e sim o principal
        // 2 - nome da tabela intermediaria
        // 3 - id correspondente na tabela intermediaria foreingkey do presente model nesse caso do Techs
        // 4 - id correspondente na tabela intermediaria foreingkey do outro model princiapl no caso do Devs

        return $this->belongsToMany('App\Http\Models\Devs', 'dev_techs', 'id_tech', 'id_dev');
    }
}
