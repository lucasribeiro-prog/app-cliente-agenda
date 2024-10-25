<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusAgendamento extends Model
{
    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class, 'status_agendamento_id');
    }
}
