<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Agendamento;

class Processo extends Model
{
    public function agendamentos()
    {
        return $this->hasOne(Agendamento::class);
    }
}
