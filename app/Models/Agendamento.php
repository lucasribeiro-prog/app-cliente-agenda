<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;
use App\Models\Atendimento;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Contato;
use App\Models\StatusAgendamento;
use App\Models\Link;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agendamento extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['id_cliente', 'id_contato', 'categoria', 'data_leilao', 'data', 'hora'];

    public function getTelefoneAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    public static function rules()
    {
        return [
            'id_usuario'  => 'exists:users,id',
            'nome' => 'required|min:3|max:40',
            'cpf' => 'required|size:11|unique:clientes,cpf',
            'matricula' => 'required',
            'telefone' => 'required|size:11',
            'atendimento' => 'exists:atendimentos,id',
            'data' => 'required|date',
            'hora' => 'required|date_format:H:i:s',
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
            'usuario.exists' =>  'Selecione uma opção válida',
        ];
    }

    public function usuarios()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function clientes()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function contatos()
    {
        return $this->belongsTo(Contato::class, 'id_contato');
    }

    public function categorias()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }

    public function atendimentos()
    {
        return $this->belongsTo(Atendimento::class, 'id_atendimento');
    }

    public function status_agendamentos()
    {
        return $this->belongsTo(StatusAgendamento::class, 'id_status');
    }

    public function links()
    {
        return $this->belongsTo(Link::class, 'id_link');
    }
}
