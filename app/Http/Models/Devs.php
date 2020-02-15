<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Devs extends Model
{
    protected $table = 'devs'; //Nome da tabela na base

    protected $primaryKey = 'id'; //Campo primary key da tabela

    protected $guarded = []; // Permitir que todos os campos sejam preenchidos

    public function posts()
    {
        // 1 - Path\Model\Tabela_Filha
        // 2 - Nome do campo de referencia na tabela filha
        // 3 - Nome do campo de referencia na tabela pai
        return $this->hasMany('App\Http\Models\Posts', 'dev_id', 'id');
    }
}
