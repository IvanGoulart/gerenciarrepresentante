<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController; // Ensure ClienteController is imported

Route::get('/', function () {
    return view('representante.representante');
})->name('representante.create');

Route::get('/clientes', function () {
    return view('cliente.cliente');
})->name('cliente.create');


