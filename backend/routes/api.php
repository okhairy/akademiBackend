<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminVigileController;

//  Route::middleware('auth:sanctum')->group(function () { 

    Route::get('/admin-vigiles', [AdminVigileController::class, 'index']); // Afficher la liste des Admins/Vigiles
    Route::post('/admin-vigiles', [AdminVigileController::class, 'store']); // Créer un nouvel Admin/Vigile
    Route::get('/admin-vigiles/{id}', [AdminVigileController::class, 'show']); // Afficher un Admin/Vigile spécifique
    Route::put('/admin-vigiles/{id}', [AdminVigileController::class, 'update']); // Mettre à jour un Admin/Vigile existant
    Route::delete('/admin-vigiles/{id}', [AdminVigileController::class, 'destroy']); // Supprimer un Admin/Vigile
    Route::patch('/admin-vigiles/bloquer/{id}', [AdminVigileController::class, 'bloquer']); // Bloquer un Admin/Vigile
    Route::patch('/admin-vigiles/debloquer/{id}', [AdminVigileController::class, 'debloquer']); // Débloquer un Admin/Vigile
    Route::delete('/admin-vigiles', [AdminVigileController::class, 'supprimerPlusieursAdminVigiles']); // Supprimer plusieurs Admins/Vigiles
    
    Route::post('/assigner-carte/{id}', [AuthController::class, 'assignerCarte']); // Assigner une carte à un étudiant
    Route::delete('/etudiants/{id}/desassigner-carte', [AuthController::class, 'desassignerCarte']);

    Route::patch('/change-mdp', [AuthController::class, 'changePwd'])->middleware('auth:sanctum');
    Route::patch('/user/update', [AuthController::class, 'updateUserInfo']);

    // Étudiant routes
    Route::post('/register/etudiant', [AuthController::class, 'registerEtudiant']);
    Route::put('/etudiant/{id}', [AuthController::class, 'updateEtudiant']);
    Route::delete('/etudiant/{id}', [AuthController::class, 'supprimerEtudiant']);
    Route::get('/etudiant/{id}', [AuthController::class, 'getEtudiantById']);
    Route::get('/etudiants', [AuthController::class, 'getAllEtudiants']);
    Route::patch('/etudiant/bloquer/{id}', [AuthController::class, 'bloquerEtudiant']);
    Route::patch('/etudiant/debloquer/{id}', [AuthController::class, 'debloquerEtudiant']);
    Route::patch('/etudiant/photo', [AuthController::class, 'updatePhoto']);

    Route::post('/etudiantc/depot/{id}', [AuthController::class, 'depot']);
    Route::post('/etudiant/retrait', [AuthController::class, 'retrait']);
    Route::post('/etudiant/acces-campus', [AuthController::class, 'accesCampus']);
    Route::patch('/etudiant/bloquer-carte', [AuthController::class, 'bloquerCarte'])->middleware('auth:sanctum');

    Route::patch('/etudiant/debloquer-carte', [AuthController::class, 'bloquerCarte']);
    Route::delete('/etudiants', [AuthController::class, 'supprimerPlusieursEtudiants']);
 

// Authentification
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::post('/envoyer-email', [AdminVigileController::class, 'sendEmail']);
Route::post('/password/forgot', [AuthController::class, 'forgotPassword']);
Route::post('/password/reset', [AuthController::class, 'resetPassword']);

/* }); */
Route::middleware('auth:sanctum')->group(function () {
Route::get('/etudiant/transactions', [AdminVigileController::class, 'getTransactions']);
});
Route::get('/etudiant/depenes', [AdminVigileController::class, 'gettransactions'])->middleware('auth:sanctum');
Route::get('/test', [AdminVigileController::class, 'test'])->middleware('auth:sanctum');
Route::get('/testest', [AuthController::class, 'test'])->middleware('auth:sanctum');
Route::get('/transactions', [AuthController::class, 'getAllTransactions'])->middleware('auth:sanctum');
Route::get('/etudiantc/week-depenses', [AuthController::class, 'getWeeklyExpenses'])->middleware('auth:sanctum');
Route::get('/etudiantc/month-depenses', [AuthController::class, 'getMonthlyExpenses'])->middleware('auth:sanctum');
Route::get('/last-depot', [AuthController::class, 'getLastDepositAndWeeklyExpenses'])->middleware('auth:sanctum');
Route::get('/depenses-mensuelles', [AuthController::class, 'getMonthlyMeals'])->middleware('auth:sanctum');
Route::get('/depenses-journee', [AuthController::class, 'getDailyMeals'])->middleware('auth:sanctum');
Route::get('/mes-depot', [AuthController::class, 'getDepots'])->middleware('auth:sanctum');
Route::get('/nbr-users', [AuthController::class, 'getTotalUsers'])->middleware('auth:sanctum');
Route::get('/etudiant/transactions', [AuthController::class, 'getTransactions'])->middleware('auth:sanctum');
Route::get('/transactions', [AuthController::class, 'getAllTransactions'])/* ->middleware('auth:sanctum'); */;
Route::get('/etudiant/week-depenses', [AuthController::class, 'getWeeklyExpenses'])->middleware('auth:sanctum');
Route::get('/etudiant/month-depenses', [AuthController::class, 'getMonthlyExpenses'])->middleware('auth:sanctum');
Route::get('/etudiant/last-depot', [AuthController::class, 'getLastDepositAndWeeklyExpenses'])->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user(); 
})->middleware('auth:sanctum'); 
Route::get('/users', [UserController::class, 'getAllUsers']);


Route::patch('/admin-vigiles/change-pwd/{id}', [AdminVigileController::class, 'changePassword']);
Route::patch('/etudiant/change-password/{id}', [AuthController::class, 'changePassword']);

Route::get('/etudiants/nombre', [AuthController::class, 'getNombreEtudiants']);//route qui calcul le nombre total d'etudiant
Route::get('/users', [AuthController::class, 'getAllUsers']);//recupere tous les utilisateurs 
Route::post('/utilisateurs/register', [AuthController::class, 'register']);//route pour enregistrer des utilisateurs
Route::put('/utilisateurs/{id}', [AuthController::class, 'updateUser']);//route pour modifeir un user selon son role
Route::get('/utilisateurs/{id}/{role}', [AuthController::class, 'getUserById']);// route qui recupere un utilisateur par son id
Route::delete('/utilisateur/{id}/{role}', [AuthController::class, 'supprimerUtilisateur']);

// Confirmations de paiement
Route::post('/payment/ipn', [PaiementController::class, 'ipn'])->name('paytech.ipn');
Route::get('/payment/success', fn() => 'Paiement réussi')->name('paytech.success');
Route::get('/payment/cancel', fn() => 'Paiement annulé')->name('paytech.cancel');



// Route pour bloquer la carte d'un étudiant ou bloquer un admin/vigile
Route::post('/bloquer/{id}', [AuthController::class, 'bloquer']);



Route::post('/bloquer/{id}', [AuthController::class, 'bloquer']);// Route pour bloquer la carte d'un étudiant ou bloquer un admin/vigile
Route::delete('/utilisateur/{id}/{role}', [AuthController::class, 'supprimerUtilisateur']);//supprimer un utilisateur selon son role
Route::delete('/supprimer-utilisateurs/{role}', [AuthController::class, 'supprimerPlusieursUtilisateurs']);//supprimer plusieurs utilisateurs selon son role


Route::post('/arduino-data', [AuthController::class, 'recevoirData']);//route pour recevoir les données de node.js

Route::middleware('auth:sanctum')->get('/utilisateur-connecte', [AuthController::class, 'getUtilisateurConnecte']);


