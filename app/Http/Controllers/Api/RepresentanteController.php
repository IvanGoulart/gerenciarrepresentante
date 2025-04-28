<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Representante;
use Illuminate\Http\Request;

class RepresentanteController extends Controller
{
    /**
     * Listar todos os representantes com filtros e paginação
     */
    public function index(Request $request)
    {
        $query = Representante::with('cidades');

        if ($search = $request->query('search')) {
            $query->where('nome', 'like', '%' . $search . '%');
        }

        if ($cidade_id = $request->query('cidade_id')) {
            $query->whereHas('cidades', function ($q) use ($cidade_id) {
                $q->where('id', $cidade_id);
            });
        }

        return response()->json($query->paginate(10), 200);
    }

    /**
     * Criar um novo representante
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cidade_id' => 'nullable|array',
            'cidade_id.*' => 'exists:cidades,id',
        ]);

        $representante = Representante::create(['nome' => $request->nome]);

        if ($request->has('cidade_id') && !empty($request->cidade_id)) {
            $representante->cidades()->attach($request->cidade_id);
        }

        return response()->json($representante->load('cidades'), 201);
    }

    public function addCidades(Request $request, $id)
    {
        $representante = Representante::findOrFail($id);

        $request->validate([
            'cidade_id' => 'required|array',
            'cidade_id.*' => 'exists:cidades,id',
        ]);

        $representante->cidades()->attach($request->cidade_id);
        return response()->json($representante->load('cidades'), 200);
    }

    /**
     * Exibir um representante específico
     */
    public function show($id)
    {
        $representante = Representante::with('cidades')->findOrFail($id);
        return response()->json($representante, 200);
    }

    /**
     * Atualizar um representante existente
     */
    public function update(Request $request, $id)
    {
        $representante = Representante::findOrFail($id);

        $request->validate([
            'nome' => 'sometimes|string|max:255',
            'cidade_id' => 'nullable|array',
            'cidade_id.*' => 'exists:cidades,id',
        ]);

        if ($request->has('nome')) {
            $representante->update(['nome' => $request->nome]);
        }

        if ($request->has('cidade_id')) {
            $representante->cidades()->sync($request->cidade_id);
        }

        return response()->json($representante->load('cidades'), 200);
    }
    /**
     * Deletar um representante
     */
    public function destroy($id)
    {
        Representante::findOrFail($id)->delete();
        return response()->json(null, 204);
    }

    public function updateCidades(Request $request, $id)
    {
        $representante = Representante::findOrFail($id);

        $request->validate([
            'cidade_id' => 'nullable|array',
            'cidade_id.*' => 'exists:cidades,id',
        ]);

        if ($request->has('cidade_id')) {
            $representante->cidades()->sync($request->cidade_id);
        } else {
            $representante->cidades()->detach();
        }

        return response()->json($representante->load('cidades'), 200);
    }
}