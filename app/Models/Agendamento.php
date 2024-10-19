<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    protected $fillable = ['id_cliente', 'id_contato', 'categoria', 'data_leilao', 'data', 'hora'];
}
