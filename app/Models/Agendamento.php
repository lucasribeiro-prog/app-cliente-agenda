<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    protected $fillable = ['id_cliente', 'id_contato', 'categoria', 'data_leilao', 'data', 'hora'];

    public static function rules()
    {
        return [
            'nome' => 'required|min:3|max:40',
            'cpf' => 'required|size:11|unique:clientes,cpf',
            'matricula' => 'required',
            'telefone' => 'required|size:11',
            'atendimento' => 'exists:atendimentos,id',
            'data' => 'required|date',
            'hora' => 'required|date_format:H:i:s',
            'data_leilao' => 'required|date',
            'categoria' => 'exists:categorias,id',
        ];
    }

    public static function feedback()
    {
        return [
            'nome.min' => 'O campo nome deve conter no minimo 3 caracteres',
            'size'  => 'O campo :attribute deve conter exatamente 11 digitos',
            'required' => 'O campo :attribute deve ser preenchido',
            'atendimento.exists'=> 'Selecione uma opção válida',
            'categoria.exists' =>  'Selecione uma opção válida',
        ];
    }
}
