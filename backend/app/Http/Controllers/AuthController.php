<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\AdminVigile;
use App\Models\Etudiant;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Transaction;
use Carbon\Carbon;

class AuthController extends Controller
{
    // Inscription d'un étudiant
    public function registerEtudiant(Request $request)
    {
        $request->validate([
            'nom' => [
                'required',
                'string',
                'regex:/^[A-Za-z0-9][A-Za-z0-9 ]*$/',
                'regex:/^(?!.*  ).*$/'
            ],
            'prenom' => [
                'required',
                'string',
                'regex:/^[A-Za-z0-9][A-Za-z0-9 ]*$/',
                'regex:/^(?!.*  ).*$/'
            ],
            'email' => [
                'required',
                'email',
                'regex:/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/',
                'unique:etudiants,email'
            ],
            'telephone' => [
                'required',
                'string',
                'regex:/^(70|75|76|77|78)[0-9]{7}$/',
                'unique:etudiants,telephone'
            ],
            'chambre' => [
                'nullable',
                'string',
                'regex:/^[A-Za-z0-9][A-Za-z0-9 ]*$/',
                'regex:/^(?!.*  ).*$/'
            ],
            'numero_de_dossier' => 'required|integer|unique:etudiants,numero_de_dossier',
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'nom.regex' => 'Le nom ne doit pas commencer par un espace, contenir deux espaces consécutifs, et ne doit contenir que des chiffres et des lettres.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'prenom.regex' => 'Le prénom ne doit pas commencer par un espace, contenir deux espaces consécutifs, et ne doit contenir que des chiffres et des lettres.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être une adresse email valide.',
            'email.regex' => 'Le format de l\'email est incorrect.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.regex' => 'Le numéro de téléphone doit être de 9 chiffres et commencer par 70, 75, 76, 77 ou 78.',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'chambre.regex' => 'La chambre ne doit pas commencer par un espace, contenir deux espaces consécutifs, et ne doit contenir que des chiffres et des lettres.',
            'numero_de_dossier.required' => 'Le numéro de dossier est obligatoire.',
            'numero_de_dossier.integer' => 'Le numéro de dossier doit être un entier.',
            'numero_de_dossier.unique' => 'Ce numéro de dossier est déjà utilisé.',
        ]);

        $etudiant = Etudiant::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'chambre' => $request->chambre,
            'numero_de_dossier' => $request->numero_de_dossier,
            'statut' => 'active', // Statut par défaut
        ]);

        return response()->json(['message' => 'Étudiant créé avec succès', 'etudiant' => $etudiant], 201);
    }

    // Mettre à jour un étudiant
    public function updateEtudiant(Request $request, $id)
    {
        try {
            
            $etudiant = Etudiant::findOrFail($id);

            $request->validate([
                'nom' => [
                    'sometimes',
                    'string',
                    'regex:/^[A-Za-z0-9][A-Za-z0-9 ]*$/',
                    'regex:/^(?!.*  ).*$/'
                ],
                'prenom' => [
                    'sometimes',
                    'string',
                    'regex:/^[A-Za-z0-9][A-Za-z0-9 ]*$/',
                    'regex:/^(?!.*  ).*$/'
                ],
                'email' => [
                    'sometimes',
                    'email',
                    'regex:/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/',
                    'unique:etudiants,email,' . $id
                ],
                'telephone' => [
                    'sometimes',
                    'string',
                    'regex:/^(70|75|76|77|78)[0-9]{7}$/',
                    'unique:etudiants,telephone,' . $id
                ],
                'chambre' => [
                    'nullable',
                    'string',
                    'regex:/^[A-Za-z0-9][A-Za-z0-9 ]*$/',
                    'regex:/^(?!.*  ).*$/'
                ],
                'numero_de_dossier' => 'sometimes|integer|unique:etudiants,numero_de_dossier,' . $id,
            ], [
                'nom.regex' => 'Le nom ne doit pas commencer par un espace, contenir deux espaces consécutifs, et ne doit contenir que des chiffres et des lettres.',
                'prenom.regex' => 'Le prénom ne doit pas commencer par un espace, contenir deux espaces consécutifs, et ne doit contenir que des chiffres et des lettres.',
                'email.email' => 'L\'email doit être une adresse email valide.',
                'email.regex' => 'Le format de l\'email est incorrect.',
                'email.unique' => 'Cet email est déjà utilisé.',
                'telephone.regex' => 'Le numéro de téléphone doit être de 9 chiffres et commencer par 70, 75, 76, 77 ou 78.',
                'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
                'chambre.regex' => 'La chambre ne doit pas commencer par un espace, contenir deux espaces consécutifs, et ne doit contenir que des chiffres et des lettres.',
                'numero_de_dossier.integer' => 'Le numéro de dossier doit être un entier.',
                'numero_de_dossier.unique' => 'Ce numéro de dossier est déjà utilisé.',
            ]);

            $etudiant->update($request->all());

            return response()->json(['message' => 'Étudiant mis à jour avec succès', 'etudiant' => $etudiant], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Étudiant introuvable'], 404);
        }
    }

    // Supprimer un étudiant
    public function supprimerEtudiant($id): JsonResponse
    {
        try {
            $etudiant = Etudiant::findOrFail($id);
            $etudiant->delete();

            return response()->json(['message' => 'Étudiant supprimé avec succès'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Étudiant introuvable'], 404);
        }
    }

    // Bloquer un étudiant
    public function bloquerEtudiant($id): JsonResponse
    {
        try {
            $etudiant = Etudiant::findOrFail($id);

            if ($etudiant->statut === 'active') {
                $etudiant->statut = 'bloqué';
                $etudiant->save();

                return response()->json(['message' => 'Étudiant bloqué avec succès'], 200);
            } else {
                return response()->json(['message' => 'Étudiant déjà bloqué'], 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Étudiant introuvable'], 404);
        }
    }

    // Débloquer un étudiant
    public function debloquerEtudiant($id): JsonResponse
    {
        try {
            $etudiant = Etudiant::findOrFail($id);

            if ($etudiant->statut === 'bloqué') {
                $etudiant->statut = 'active';
                $etudiant->save();

                return response()->json(['message' => 'Étudiant débloqué avec succès'], 200);
            } else {
                return response()->json(['message' => 'Étudiant déjà actif'], 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Étudiant introuvable'], 404);
        }
    }

    public function changePassword(Request $request, $id): JsonResponse
    {
        $validatedData = $request->validate([
            'nouveau_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/', // doit contenir au moins une lettre majuscule
                'regex:/[a-z]/', // doit contenir au moins une lettre minuscule
                'regex:/[0-9]/', // doit contenir au moins un chiffre
                'regex:/[@$!%*?&]/' // doit contenir au moins un caractère spécial
            ],
        ], [
            'nouveau_password.required' => 'Le nouveau mot de passe est obligatoire.',
            'nouveau_password.min' => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
            'nouveau_password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'nouveau_password.regex' => 'Le nouveau mot de passe doit contenir au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.',
        ]);

        try {
            $etudiant = Etudiant::findOrFail($id);
            $etudiant->mot_de_passe = $validatedData['nouveau_password'];
            $etudiant->save();

            return response()->json(['message' => 'Mot de passe changé avec succès']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Utilisateur introuvable'], 404);
        }
    }

    // Authentification
    public function login(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
                'email',
                'regex:/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/'
            ],
            'password' => 'required|string|min:8',
        ], [
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être une adresse email valide.',
            'email.regex' => 'Le format de l\'email est incorrect.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        $credentials = $request->only('email', 'password');

        // Essayer de se connecter en tant qu'admin/vigile
        if (Auth::guard('admin_vigile')->attempt($credentials)) {
            $user = Auth::guard('admin_vigile')->user();

            if ($user->statut === 'bloqué') {
                return response()->json(['error' => 'Utilisateur bloqué'], 403);
            }

            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
                'role' => $user->role,
            ]);
        }

        // Essayer de se connecter en tant qu'étudiant
        if (Auth::guard('etudiant')->attempt($credentials)) {
            $user = Auth::guard('etudiant')->user();

            if ($user->statut === 'bloqué') {
                return response()->json(['error' => 'Utilisateur bloqué'], 403);
            }

            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
                'role' => 'etudiant',
            ]);
        }

        // Si l'authentification échoue
        return response()->json(['error' => 'L\'authentification a échoué'], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Deconnexion reussie']);
    }


    public function depot(Request $request, $id): JsonResponse
    {
        $request->validate([
            'montant' => 'required|integer|min:50',
            'operateur' => 'required|in:wave,orange,free',
        ], [
            'montant.required' => 'Le montant est obligatoire.',
            'montant.integer' => 'Le montant doit être un entier.',
            'montant.min' => 'Le montant doit être au moins 1.',
            'operateur.required' => 'L\'opérateur est obligatoire.',
            'operateur.in' => 'L\'opérateur doit être wave, orange ou free.',
        ]);

        try {
            $etudiant = Etudiant::findOrFail($id);

            // Vérifier si l'étudiant a un uid_carte
            if (empty($etudiant->uid_carte)) {
                return response()->json(['message' => 'Désolé, Veuillez disposer d\'une carte s\'il vous plait'], 400);
            }

            // Incrémenter le solde de l'étudiant
            $etudiant->solde += $request->montant;
            $etudiant->save();

            // Enregistrer la transaction
            $transaction = Transaction::create([
                'date' => now(),
                'montant' => $request->montant,
                'type' => 'dépot',
                'operateur' => $request->operateur,
                'id_etudiant' => $etudiant->id,
            ]);

            return response()->json(['message' => 'Dépôt effectué avec succès', 'transaction' => $transaction], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Étudiant introuvable'], 404);
        }
    }


    public function retrait(Request $request): JsonResponse
    {
        $request->validate([
            'uid_carte' => 'required|string',
        ], [
            'uid_carte.required' => 'Le uid_carte est obligatoire.',
        ]);
        try {
            $etudiant = Etudiant::where('uid_carte', $request->uid_carte)->firstOrFail();

            // Vérifier si l'étudiant n'est pas bloqué
            if ($etudiant->statut === 'bloqué') {
                return response()->json(['message' => 'Étudiant bloqué'], 403);
            }

            // Vérifier si la carte n'est pas bloquée
            if ($etudiant->status_carte === 'bloqué') {
                return response()->json(['message' => 'Accès refusé: Carte bloquée'], 403);
            }

            $currentTime = Carbon::now();
            $currentHour = $currentTime->hour;
            $currentMinute = $currentTime->minute;

            $montant = 0;
            $type = '';

            if (($currentHour == 9 && $currentMinute >= 0) || ($currentHour == 11 && $currentMinute <= 30) || ($currentHour > 9 && $currentHour < 11)) {
                $montant = 50;
                $type = 'petit déjeuner';
            } elseif (($currentHour == 12 && $currentMinute >= 0) || ($currentHour == 14 && $currentMinute <= 0) || ($currentHour > 12 && $currentHour < 14)) {
                $montant = 100;
                $type = 'déjeuner';
            } elseif (($currentHour == 19 && $currentMinute >= 0) || ($currentHour == 1 && $currentMinute <= 0) || ($currentHour > 19 || $currentHour < 1)) {
                $montant = 100;
                $type = 'dîner';
            } else {
                return response()->json(['message' => 'Retrait non autorisé à cette heure'], 400);
            }

            // Vérifier si l'étudiant a suffisamment de solde
            if ($etudiant->solde < $montant) {
                return response()->json(['message' => 'Solde insuffisant'], 400);
            }

            // Débiter le solde de l'étudiant
            $etudiant->solde -= $montant;
            $etudiant->save();

            // Enregistrer la transaction
            $transaction = Transaction::create([
                'date' => now(),
                'montant' => $montant,
                'type' => $type,
                'operateur' => null,
                'id_etudiant' => $etudiant->id,
            ]);

            return response()->json(['message' => 'Retrait effectué avec succès', 'transaction' => $transaction], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Carte invalide'], 404);
        }
    }

    // Accès au campus
    public function accesCampus(Request $request): JsonResponse
    {
        $request->validate([
            'uid_carte' => 'required|string',
        ], [
            'uid_carte.required' => 'Le uid_carte est obligatoire.',
        ]);

        try {
            $etudiant = Etudiant::where('uid_carte', $request->uid_carte)->firstOrFail();

            // Vérifier si l'étudiant n'est pas bloqué
            if ($etudiant->statut === 'bloqué') {
                return response()->json(['message' => 'Accès refusé : Étudiant bloqué'], 403);
            }

            // Vérifier si la carte n'est pas bloquée
            if ($etudiant->status_carte === 'bloqué') {
                return response()->json(['message' => 'Carte bloquée'], 403);
            }

            return response()->json(['message' => 'Accès autorisé'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Carte invalide'], 404);
        }
    }

    public function getTransactions(Request $request): JsonResponse
    {
        $etudiant = Auth::guard('etudiant')->user();

        if (!$etudiant) {
            return response()->json(['message' => 'Utilisateur non connecté'], 401);
        }

        $transactions = Transaction::where('id_etudiant', $etudiant->id)->get();

        return response()->json(['transactions' => $transactions], 200);
    }

    public function getAllTransactions(): JsonResponse
    {
        $transactions = Transaction::all();

        if ($transactions->isEmpty()) {
            return response()->json(['message' => 'Aucune transaction pour le moment'], 200);
        }

        return response()->json(['transactions' => $transactions], 200);
    }
}