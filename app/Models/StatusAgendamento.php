<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Agendamento;

class StatusAgendamento extends Model
{
    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class, 'id_status', 'id');
    }
}
