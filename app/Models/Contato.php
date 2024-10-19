<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Contato extends Model
{
    protected $fillable = ['id_cliente', 'telefone'];

    // Mutator para criptografar o telefone antes de salvar no banco
    public function setTelefoneAttribute($value)
    {
        $this->attributes['telefone'] = Crypt::encryptString($value);
    }

    // Accessor para descriptografar o telefone ao recuperar do banco
    public function getTelefoneAttribute($value)
    {
        return Crypt::decryptString($value);
    }
}
