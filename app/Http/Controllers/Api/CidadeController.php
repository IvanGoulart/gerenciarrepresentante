<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cidade;
use Illuminate\Http\Request;

class CidadeController extends Controller
{
    /**
     * Listar todas as cidades
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $query = Cidade::with('estado');

        if ($search) {
            $query->where('nome', 'like', "%{$search}%");
        }

        $cidades = $query->paginate(10);

        return response()->json($cidades, 200);
    }

    /**
     * Exibir uma cidade específica
     */
    public function show($id)
    {
        $cidade = Cidade::findOrFail($id);
        return response()->json($cidade, 200);
    }

    /**
     * Criar uma nova cidade
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:cidades,nome',
            'estado_id' => 'required|exists:estados,id',
        ]);

      $cidade = Cidade::create($request->only(['nome', 'estado_id']));


        return response()->json($cidade, 201);
    }

    /**
     * Atualizar uma cidade existente
     */
    public function update(Request $request, $id)
    {

        $cidade = Cidade::findOrFail($id);

        $request->validate([
            'nome' => 'required|string|max:255,' . $id,
        ]);

        $cidade->update($request->only(['nome', 'estado_id']));
        return response()->json($cidade, 200);
    }

    /**
     * Deletar uma cidade
     */
    public function destroy($id)
    {
        $cidade = Cidade::findOrFail($id);
        $cidade->delete();
        return response()->json(null, 204);
    }

    /**
     * Listar representantes de uma cidade
     */
    public function representantes($id)
    {
        $cidade = Cidade::with('representantes')->findOrFail($id);
        return response()->json($cidade->representantes, 200);
    }
}
?>