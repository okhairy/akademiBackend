<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminVigileController;

Route::get('/admin-vigiles', [AdminVigileController::class, 'index']); // Afficher la liste des Admins/Vigiles
Route::post('/admin-vigiles', [AdminVigileController::class, 'store']); // Créer un nouvel Admin/Vigile
Route::get('/admin-vigiles/{id}', [AdminVigileController::class, 'show']); // Afficher un Admin/Vigile spécifique
Route::put('/admin-vigiles/{id}', [AdminVigileController::class, 'update']); // Mettre à jour un Admin/Vigile existant
Route::delete('/admin-vigiles/{id}', [AdminVigileController::class, 'destroy']); // Supprimer un Admin/Vigile
Route::patch('/admin-vigiles/bloquer/{id}', [AdminVigileController::class, 'bloquer']); // Bloquer un Admin/Vigile
Route::patch('/admin-vigiles/debloquer/{id}', [AdminVigileController::class, 'debloquer']); // Débloquer un Admin/Vigile
Route::patch('/admin-vigiles/change-pwd/{id}', [AdminVigileController::class, 'changePassword']);


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
