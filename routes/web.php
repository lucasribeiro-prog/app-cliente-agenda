<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Auth/Login'); // Alterado para renderizar a página Home
});

Route::get('/home', function () {
    return Inertia::render('Home'); // Alterado para renderizar a página Home
})->middleware(['auth', 'verified'])->name('home');

Route::get('/agendar', function () {
    return Inertia::render('Agendar'); // Adicionada a rota para a página Agendar
})->middleware(['auth']); // Você pode adicionar middleware de autenticação se necessário

Route::get('/consultar', function () {
    return Inertia::render('Consultar'); // Adicionada a rota para a página Consultar
})->middleware(['auth']); // Você pode adicionar middleware de autenticação se necessário

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
