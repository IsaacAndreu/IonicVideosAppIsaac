<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiMultimediaController;
use App\Http\Controllers\Api\Auth\ApiAuthController;

// ✅ RUTES D’AUTENTICACIÓ
Route::post('/login', [ApiAuthController::class, 'login']);
Route::post('/register', [ApiAuthController::class, 'register']); // ✅ Afegida aquesta línia
Route::middleware('auth:sanctum')->post('/logout', [ApiAuthController::class, 'logout']);

// ✅ RUTA PER SABER QUI ESTÀ AUTENTICAT
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// 📁 RUTES PÚBLIQUES PER MULTIMÈDIA
Route::get('/multimedia', [ApiMultimediaController::class, 'index'])->name('api.multimedia.index');
Route::get('/multimedia/{id}', [ApiMultimediaController::class, 'show']);

// 🔐 RUTES PROTEGIDES AMB TOKEN
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/multimedia', [ApiMultimediaController::class, 'store']);
    Route::put('/multimedia/{id}', [ApiMultimediaController::class, 'update']);
    Route::delete('/multimedia/{id}', [ApiMultimediaController::class, 'destroy']);
    Route::get('/user/multimedia', [ApiMultimediaController::class, 'myFiles']);
});
