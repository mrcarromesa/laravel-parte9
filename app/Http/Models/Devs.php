<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Devs extends Model
{
    protected $table = 'devs'; //Nome da tabela na base

    protected $primaryKey = 'id'; //Campo primary key da tabela

    protected $guarded = []; // Permitir que todos os campos sejam preenchidos
}
