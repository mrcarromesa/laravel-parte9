<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Devs extends Model
{
    protected $table = 'devs'; //Nome da tabela na base

    protected $primaryKey = 'id'; //Campo primary key da tabela

    protected $guarded = []; // Permitir que todos os campos sejam preenchidos

    public function techs()
    {
        // Parametros:
        // 1 - Classe do outro model principal, nao o intermediario e sim o principal
        // 2 - nome da tabela intermediaria
        // 3 - id correspondente na tabela intermediaria foreingkey do presente model nesse caso do Devs
        // 4 - id correspondente na tabela intermediaria foreingkey do outro model princiapl no caso do Techs

        return $this->belongsToMany('App\Http\Models\Techs', 'dev_techs', 'id_dev', 'id_tech')
            ->withPivot('status', 'id')
            ->using('App\Http\Models\Pivot\DevTechs');
    }


    public function posts()
    {
        // 1 - Path\Model\Tabela_Filha
        // 2 - Nome do campo de referencia na tabela filha
        // 3 - Nome do campo de referencia na tabela pai
        return $this->hasMany('App\Http\Models\Posts', 'dev_id', 'id');
    }
}
