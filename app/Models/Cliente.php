<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use App\Models\Contato;

class Cliente extends Model
{
    protected $fillable = ['nome', 'cpf', 'matricula'];

    //Criptografar o CPF antes de salvar no banco
    public function setCpfAttribute($value)
    {
    $this->attributes['cpf'] = Crypt::encryptString($value);
    }

    //Descriptografar o CPF ao recuperar do banco
    public function getCpfAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    public function contatos()
    {
        return $this->hasMany(Contato::class, 'id_cliente', 'id');
    }
}
