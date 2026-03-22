<?php

// routes/api.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceController;

// Rutas Públicas de Autenticación
Route::post('/login', [AuthController::class, 'login']);

// Verificación OTP con token temporal (capacidad auth:otp)
Route::post('/otp/verify', [AuthController::class, 'verifyOtp'])
    ->middleware(['auth:sanctum', 'ability:auth:otp']);

// Grupo de rutas que requieren un token definitivo con acceso completo a la API
Route::middleware(['auth:sanctum', 'ability:access:api'])->group(function () {
    Route::apiResource('services', ServiceController::class);
});
