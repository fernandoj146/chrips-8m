<?php

use App\Http\Controllers\ChirpController;
use App\Http\Controllers\MemeController;
use Illuminate\Support\Facades\Route;

// Ruta pública
Route::get('/', [MemeController::class, 'index']);

// TODO: Aquí irán las rutas de autenticación (register, login, logout)
// Seguir GUIA_AUTH_CLASE.md paso 3

// Rutas de memes (sin protección todavía)
// TODO: Proteger estas rutas con middleware 'auth' (ver guía paso 5)
Route::post('/memes', [MemeController::class, 'store']);
Route::get('/memes/{meme}/edit', [MemeController::class, 'edit']);
Route::put('/memes/{meme}', [MemeController::class, 'update']);
Route::delete('/memes/{meme}', [MemeController::class, 'destroy']);

// Chirps routes (still available)
Route::get('/chirps', [ChirpController::class, 'index']);
Route::post('/chirps/store', [ChirpController::class, 'store']);

