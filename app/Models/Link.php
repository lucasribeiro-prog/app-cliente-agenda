<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Agendamento;

class Link extends Model
{
    public function agendamentos()
    {
        return $this->hasOne(Agendamento::class);
    }
}
