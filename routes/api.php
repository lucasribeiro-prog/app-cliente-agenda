<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\RegisteredUserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('register', RegisteredUserController::class);

    Route::resource('agendar', \App\Http\Controllers\AgendamentoController::class);
    Route::resource('cliente', \App\Http\Controllers\ClienteController::class);
    Route::resource('contato', \App\Http\Controllers\ContatoController::class);

    Route::put('agendar/{agendar}/reschedule', [\App\Http\Controllers\AgendamentoController::class, 'reschedule']);
    Route::get('consultar', [\App\Http\Controllers\AgendamentoController::class, 'consultar']);
});
