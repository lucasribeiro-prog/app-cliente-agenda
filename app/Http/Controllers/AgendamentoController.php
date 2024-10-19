<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Contato;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class AgendamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        $agendamentos = Agendamento::with([
            'usuarios:id,name',
            'clientes:id,nome',
            'contatos:id,telefone',
            'categorias:id,categoria',
            'atendimentos:id,antendimento'
        ])->get();

        return response()->json($agendamentos);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Formatando o cpf e telefone
        $request->merge([
            'cpf' => preg_replace('/[^0-9]/', '', $request->get('cpf')),
            'telefone' => preg_replace('/[^0-9]/', '', $request->input('telefone')), 
        ]);

        //Validando os dados
        $request->validate(Agendamento::rules(), Agendamento::feedback());

        //Instanciando os dados no banco
        $cliente = new Cliente();
        $cliente->nome = $request->get('nome');
        $cliente->cpf = $request->get('cpf');
        $cliente->matricula = $request->get('matricula');
        $cliente->save();

        $contato = new Contato();
        $contato->id_cliente = $cliente->id;
        $contato->telefone = $request->get('telefone');
        $contato->save();

        $usuario  =  new User();

        $agendamento = new Agendamento();
        $agendamento->id_usuario = $request->get('id_usuario');
        $agendamento->id_cliente = $cliente->id;
        $agendamento->id_contato = $contato->id;
        $agendamento->id_atendimento = $request->get('atendimento');
        $agendamento->data = $request->get('data');
        $agendamento->data_leilao = $request->get('data_leilao');
        $agendamento->hora = $request->get('hora');
        $agendamento->id_categoria = $request->get('categoria');
        $agendamento->save();

        return response()->json(['success' => true, 'data' => $agendamento]);

    }

    /**
     * Display the specified resource.
     */
    public function show(Agendamento $agendamento)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Agendamento $agendamento)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Agendamento $agendamento)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agendamento $agendamento)
    {
        //
    }
}
