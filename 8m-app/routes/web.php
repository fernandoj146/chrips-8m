<?php

use App\Http\Controllers\ChirpController;
use App\Http\Controllers\MemeController;
use App\Http\Controllers\Auth\Register;
use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\Logout;
use Illuminate\Support\Facades\Route;

Route::get('/', [MemeController::class, 'index']);

// Auth routes
Route::view('/register', 'auth.register')->middleware('guest')->name('register');
Route::post('/register', Register::class)->middleware('guest');
Route::view('/login', 'auth.login')->middleware('guest')->name('login');
Route::post('/login', Login::class)->middleware('guest');

// Rutas para Auth
Route::post('/logout', Logout::class)->middleware('auth');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::post('/memes', [MemeController::class, 'store']);
    Route::get('/memes/{meme}/edit', [MemeController::class, 'edit']);
    Route::put('/memes/{meme}', [MemeController::class, 'update']);
    Route::delete('/memes/{meme}', [MemeController::class, 'destroy']);
});

// Chirps routes (still available)
Route::get('/chirps', [ChirpController::class, 'index']);
Route::post('/chirps/store', [ChirpController::class, 'store']);

