<?php

use App\Http\Controllers\AuthController; 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController; 
use App\Http\Controllers\PlantelController; 
use App\Http\Controllers\ImportarPlantelesController; 
use App\Http\Controllers\MunicipioController; 
use App\Http\Controllers\LocalidadController; 

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
    Route::get('/planteles/plantilla', [ImportarPlantelesController::class, 'plantilla']);
    Route::post('/planteles/importar', [ImportarPlantelesController::class, 'importar']);
    Route::get('/municipios', [MunicipioController::class, 'index']); 
    Route::get('/municipios/plantilla', [MunicipioController::class, 'plantilla']); 
    Route::post('/municipios/importar', [MunicipioController::class, 'importar']);
    Route::get('/localidades/municipios', [LocalidadController::class, 'municipios']);
    Route::get('/localidades', [LocalidadController::class, 'index']);
    Route::post('/localidades', [LocalidadController::class, 'store']);
    Route::put('/localidades/{localidad}', [LocalidadController::class, 'update']);
    Route::delete('/localidades/{localidad}', [LocalidadController::class, 'destroy']);
});

Route::get('/{any}', fn() => view('app'))->where('any', '.*');
