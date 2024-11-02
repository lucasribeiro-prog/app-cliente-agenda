<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Contato;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Inertia\Inertia;

class AgendamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        $agendamentos = Agendamento::with([
            'usuarios:id,name',
            'clientes:id,nome,cpf,matricula',
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
        if ($request->has('agendamento_id')) {
            
            $agendamento = Agendamento::find($request->get('agendamento_id'));
    
            if (!$agendamento) {
                return response()->json(['error' => 'Agendamento não encontrado.'], 404);
            }
    
            $agendamento->id_status = $request->get('status');
            $agendamento->observacao = $request->get('observacao');
            $agendamento->save();
    
            return response()->json(['success' => true, 'message' => 'Status e observação atualizados com sucesso.', 'data' => $agendamento], 200);
    
        } else {
    
            // Formata o CPF, telefone e hora
            $horaFormatada = Carbon::createFromFormat('H:i', $request->get('hora'))->format('H:i:s');
            $request->merge([
                'cpf' => preg_replace('/[^0-9]/', '', $request->get('cpf')),
                'telefone' => preg_replace('/[^0-9]/', '', $request->get('telefone')),
                'hora' => $horaFormatada,
            ]);
    
            // Valida os dados
            $request->validate(Agendamento::rules(), Agendamento::feedback());
    
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
            $agendamento->id_usuario = $request->get('id_usuario');
            $agendamento->id_cliente = $cliente->id;
            $agendamento->id_contato = $contato->id;
            $agendamento->id_atendimento = $request->get('atendimento');
            $agendamento->data = $request->get('data');
            $agendamento->data_leilao = $request->get('data_leilao');
            $agendamento->hora = $request->get('hora');
            $agendamento->id_categoria = $request->get('categoria');
            $agendamento->save();
    
            return response()->json(['success' => true, 'message' => 'Agendamento criado com sucesso.', 'data' => $agendamento], 201);
        }
    }
    

    /**
     * Display the specified resource.
     */
    public function show(Agendamento $agendar)
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
    public function update(Request $request, Agendamento $agendar)
    {

        $agendamento = $agendar->load([
            'clientes:id,nome,cpf,matricula',
            'contatos:id,telefone',
            'status_agendamentos:id,status'
        ]);

        //Formatando o cpf e telefone
        $request->merge([
            'cpf' => preg_replace('/[^0-9]/', '', $request->get('cpf')),
            'telefone' => preg_replace('/[^0-9]/', '', $request->get('telefone')),
        ]);

        $cliente = $agendamento->clientes;
        $cliente->nome = $request->get('nome');
        $cliente->cpf = $request->get('cpf');
        $cliente->matricula = $request->get('matricula');
        $cliente->save();

        $contato = $agendamento->contatos;
        $contato->telefone = $request->get('telefone');
        $contato->save();

        $agendamento->data = $request->get('data');
        $agendamento->hora = $request->get('hora');
        $agendamento->save();

        return response()->json([
            'sucesso' => 'Dados atualizados com sucesso.',
            'agendamento' => $agendamento,
        ], 200);
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agendamento $agendar)
    {
        $agendar->delete();
        return ['msg' => 'Agendamento removido com sucesso!'];
    }

    public function reschedule(Request $request, Agendamento $agendar)
    {
        $agendar->data = $request->get('data');
        $agendar->hora = $request->get('hora');
        $agendar->id_status = null;
        $agendar->save();

        return response()->json([
            'sucesso' => 'Reagendamento realizado com sucesso.',
            'agendamento' => $agendar,
        ], 200);
    }

}
