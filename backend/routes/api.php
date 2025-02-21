<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Authentification
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Routes pour les admins/vigiles
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/dashboard', function () {
        return response()->json(['message' => 'Admin Dashboard']);
    })->middleware('role:admin');

    Route::get('/vigile/dashboard', function () {
        return response()->json(['message' => 'Vigile Dashboard']);
    })->middleware('role:vigile');
});

// Routes pour les étudiants
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/etudiant/dashboard', function () {
        return response()->json(['message' => 'Étudiant Dashboard']);
    })->middleware('role:etudiant');
});
// Inscription
Route::post('/register/admin', [AuthController::class, 'registerAdmin']);
Route::post('/register/vigile', [AuthController::class, 'registerVigile']);
Route::post('/register/etudiant', [AuthController::class, 'registerEtudiant']);