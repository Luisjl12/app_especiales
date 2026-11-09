<?php

use App\Http\Controllers\AuthController; 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController; 
use App\Http\Controllers\PlantelController; 

Route::get('/', fn () =>view('app')); 

Route::get('/usuario', [AuthController::class, 'usuario']); 
Route::post('/login', [AuthController::class, 'login']); 
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function(){
    Route::get('/usuarios', [UsuarioController::class, 'index']); 
    Route::put('/usuarios/{user}/id', [UsuarioController::class, 'cambiarRol']); 
    Route::post('/usuarios', [UsuarioController::class, 'store']); 
    Route::put('/usuarios/{user}', [UsuarioController::class, 'update']);
    Route::delete('/usuarios/{user}', [UsuarioController::class, 'destroy']);
    Route::get('/planteles', [PlantelController::class, 'index']); 
    Route::post('/planteles', [PlantelController::class, 'store']); 
    Route::put('/planteles/{plantel}', [PlantelController::class, 'update']); 
    Route::delete('/planteles/{plantel}', [PlantelController::class, 'destroy']); 
});

Route::get('/{any}', fn() => view('app'))->where('any', '.*');
