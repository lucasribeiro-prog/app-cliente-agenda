<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Contato extends Model
{
    protected $fillable = ['id_cliente', 'telefone'];

    // Criptografar o telefone antes de salvar no banco
    public function setTelefoneAttribute($value)
    {
        $this->attributes['telefone'] = Crypt::encryptString($value);
    }

    // Descriptografar o telefone ao recuperar do banco
    public function getTelefoneAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    public function clientes()
    {
        return $this->belongsTo('App\Models\Cliente');
    }
}
