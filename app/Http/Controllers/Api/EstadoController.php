<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Estado;

class EstadoController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->query('search');
        $query = Estado::query();

        if ($search) {
            $query->where('nome', 'like', "%{$search}%")
                  ->orWhere('uf', 'like', "%{$search}%");
        }

        // Retorna todos os registros sem paginação
        $estados = $query->get();

        return response()->json($estados, 200);

    }
}
