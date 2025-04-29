<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Listar todos os clientes com filtro e paginação
     */
    public function index(Request $request)
    {
        $query = Cliente::with('cidade.estado');

        // Filtro por nome ou email
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Filtro por cidade_id
        if ($cidade_id = $request->query('cidade_id')) {
            $query->where('cidade_id', $cidade_id);
        }

        // Paginação (10 registros por página)
        $clientes = $query->paginate(10);

        return response()->json($clientes, 200);
    }


    /**
     * Exibir um cliente específico
     */
    public function show($id)
    {
        $cliente = Cliente::with('cidade')->findOrFail($id);
        return response()->json($cliente, 200);
    }

    /**
     * Criar um novo cliente
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email',
            'cidade_id' => 'required|exists:cidades,id',
        ]);

        $cliente = Cliente::create($request->only(['nome', 'email', 'cidade_id']));
        return response()->json($cliente, 201);
    }

    /**
     * Atualizar um cliente existente
     */
    public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);

        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email,' . $id,
            'cidade_id' => 'required|exists:cidades,id',
        ]);

        $cliente->update($request->only(['nome', 'email', 'cidade_id']));
        return response()->json($cliente, 200);
    }

    /**
     * Deletar um cliente
     */
    public function destroy($id)
    {
        Cliente::findOrFail($id)->delete();
        return response()->json(null, 204);
    }

    /**
     * Listar representantes de um cliente
     */
    public function representantes($id)
    {
        $representantes = \App\Models\Representante::whereHas('clientes', function ($query) use ($id) {
            $query->where('cliente_id', $id);
        })->get(['id', 'nome']);

        return response()->json($representantes, 200);
    }
}
?>