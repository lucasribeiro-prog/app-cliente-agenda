<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Contato;
use Illuminate\Http\Request;

class AgendamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        //dd($request->all());
        $cliente = new Cliente();
        $cliente->nome = $request->get('nome');
        $cliente->cpf = $request->get('cpf');
        $cliente->matricula = $request->get('matricula');
        $cliente->save();

        $contato = new Contato();
        $contato->id_cliente = $cliente->id;
        $contato->telefone = $request->get('telefone');
        $contato->save();

        $agendamento = new Agendamento();
        $agendamento->id_cliente = $cliente->id;
        $agendamento->id_contato = $contato->id;
        $agendamento->id_atendimento = $request->get('atendimento');
        $agendamento->data = $request->get('data');
        $agendamento->data_leilao = $request->get('data_leilao');
        $agendamento->hora = $request->get('hora');
        $agendamento->id_categoria = $request->get('categoria');
        $agendamento->save();

        return response()->json($agendamento);

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
