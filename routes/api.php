<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\CidadeController;
use App\Http\Controllers\Api\RepresentanteController;
use App\Http\Controllers\Api\EstadoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rotas para Clientes
Route::get('/clientes', [ClienteController::class, 'index']); // Listar todos os clientes
Route::get('/clientes/{id}', [ClienteController::class, 'show']); // Exibir um cliente específico
Route::post('/clientes', [ClienteController::class, 'store']); // Criar um cliente
Route::put('/clientes/{id}', [ClienteController::class, 'update']); // Atualizar um cliente
Route::delete('/clientes/{id}', [ClienteController::class, 'destroy']); // Deletar um cliente

// Rotas para Cidades

Route::get('/cidades', [CidadeController::class, 'index']); // Listar todas as cidades
Route::get('/cidades/{id}', [CidadeController::class, 'show']); // Exibir uma cidade específica
Route::post('/cidades', [CidadeController::class, 'store']); // Criar uma cidade
Route::put('/cidades/{id}', [CidadeController::class, 'update']); // Atualizar uma cidade
Route::delete('/cidades/{id}', [CidadeController::class, 'destroy']); // Deletar uma cidade
Route::post('/representantes/{id}/cidades', [RepresentanteController::class, 'addCidades']); // Adicionar cidades a um representante
Route::put('/representantes/{id}/cidades', [RepresentanteController::class, 'updateCidades']);

// Rotas para Representantes
Route::get('/representantes', [RepresentanteController::class, 'index']); // Listar todos os representantes
Route::get('/representantes/{id}', [RepresentanteController::class, 'show']); // Exibir um representante específico
Route::post('/representantes', [RepresentanteController::class, 'store']); // Criar um representante
Route::put('/representantes/{id}', [RepresentanteController::class, 'update']); // Atualizar um representante
Route::delete('/representantes/{id}', [RepresentanteController::class, 'destroy']); // Deletar um representante

// Rotas específicas para consultas solicitadas
Route::get('/clientes/{id}/representantes', [ClienteController::class, 'representantes']); // Representantes de um cliente
Route::get('/cidades/{id}/representantes', [CidadeController::class, 'representantes']); // Representantes de uma cidade
//estados
Route::get('/estados', [EstadoController::class, 'index']);
?>
