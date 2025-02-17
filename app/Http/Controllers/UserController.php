<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::get();
        return response()->json($users);
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {   
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|integer|in:1,2',
        ]);

        $usuario = $user;
        $usuario->name = $request->input('name');
        $usuario->email = $request->input('email');
        if ($request->role == 1) {
            $usuario->role = 'PADRÃO';
        } elseif ($request->role == 2) {
            $usuario->role = 'ADMIN';
        }
        $usuario->save();

        return response()->json([
            'sucesso' => 'Dados atualizados com sucesso.',
            'user' => $usuario,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $usuario = User::find($id);
        if ($usuario) {
            $usuario->delete();
            return response()->json(['message' => 'Usuário excluído com sucesso!'], 200);
        }
        return response()->json(['message' => 'Usuário não encontrado.'], 404);
    }
}
