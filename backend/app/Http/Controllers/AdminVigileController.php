<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminVigile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Mail\MonEmail;
use Illuminate\Support\Facades\Mail;
use App\Mail\BienvenueEmail;
use Illuminate\Support\Str;


class AdminVigileController extends Controller
{
    public function sendEmail(Request $request)
    {
        // Les données à injecter dans l'email
        $details = [
            'titre' => 'Salut depuis Laravel',
            'message' => 'Voici un message envoyé depuis Gmail SMTP !'
        ];

        // Envoi de l'email
        Mail::to('asow19133@gmail.com')->send(new MonEmail($details));

        return response()->json(['message' => 'Email envoyé avec succès !']);
    }

    /**
     * Afficher la liste des Admins/Vigiles
     */
    public function index(): JsonResponse
    {
        $adminVigiles = AdminVigile::all();

        if ($adminVigiles->isEmpty()) {
            return response()->json(['message' => 'Aucun utilisateur inscrit pour le moment'], 200);
        }

        return response()->json($adminVigiles);
    }

    /**
     * Stocker un nouvel Admin/Vigile
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
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
            'email' => 'required|string|email|unique:admin_vigiles',
            'telephone' => [
                'required',
                'string',
                'regex:/^(70|75|76|77|78)[0-9]{7}$/',
                'unique:admin_vigiles'
            ],
            'mot_de_passe' => 'string|min:8',
            'statut' => 'in:active,bloqué',
            'role' => 'required|in:admin,vigile'
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'nom.regex' => 'Le nom ne doit pas commencer par un espace, contenir deux espaces consécutifs, et ne doit contenir que des chiffres et des lettres.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'prenom.regex' => 'Le prénom ne doit pas commencer par un espace, contenir deux espaces consécutifs, et ne doit contenir que des chiffres et des lettres.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être une adresse email valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.regex' => 'Le numéro de téléphone doit être de 9 chiffres et commencer par 70, 75, 76, 77 ou 78.',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'mot_de_passe.required' => 'Le mot de passe est obligatoire.',
            'mot_de_passe.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'statut.in' => 'Le statut doit être soit "active" soit "bloqué".',
            'role.required' => 'Le rôle est obligatoire.',
            'role.in' => 'Le rôle doit être soit "admin" soit "vigile".'
        ]);

        // Générer un mot de passe fort de 8 caractères
        $plainPassword = Str::random(8); // Générer un mot de passe aléatoire
            // Créer l'Admin Vigile et hacher le mot de passe
        $adminVigile = AdminVigile::create([
            'nom' => $validatedData['nom'],
            'prenom' => $validatedData['prenom'],
            'email' => $validatedData['email'],
            'telephone' => $validatedData['telephone'],
            'mot_de_passe' => $plainPassword, // Hacher le mot de passe avant de le stocker
            'statut' => 'active',
            'role' => $validatedData['role'],
            'date_de_creation' => now(), // Si tu veux ajouter un timestamp
        ]);

            // Données pour l'email
            $details = [
                'nom' => $adminVigile->nom,
                'prenom' => $adminVigile->prenom,
                'email' => $adminVigile->email,
                'mot_de_passe' => $plainPassword // Affichage en clair pour l'email
            ];

            // Envoi de l'email de bienvenue
            Mail::to($adminVigile->email)->send(new BienvenueEmail($details));
        return response()->json($adminVigile, 201);
    }

    /**
     * Afficher un Admin/Vigile spécifique
     */
    public function show($id): JsonResponse
    {
        try {
            $adminVigile = AdminVigile::findOrFail($id);
            return response()->json($adminVigile);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Utilisateur introuvable'], 404);
        }
    }

    /**
     * Mettre à jour un Admin/Vigile existant
     */
    public function update(Request $request, $id): JsonResponse
    {
        $adminVigile = AdminVigile::findOrFail($id);

        $validatedData = $request->validate([
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
            'email' => 'sometimes|string|email|unique:admin_vigiles,email,' . $id,
            'telephone' => [
                'sometimes',
                'string',
                'regex:/^(70|75|76|77|78)[0-9]{7}$/',
                'unique:admin_vigiles,telephone,' . $id
            ],
            'statut' => 'sometimes|in:active,bloqué',
            'role' => 'sometimes|in:admin,vigile'
        ], [
            'nom.regex' => 'Le nom ne doit pas commencer par un espace, contenir deux espaces consécutifs, et ne doit contenir que des chiffres et des lettres.',
            'prenom.regex' => 'Le prénom ne doit pas commencer par un espace, contenir deux espaces consécutifs, et ne doit contenir que des chiffres et des lettres.',
            'email.email' => 'L\'email doit être une adresse email valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'telephone.regex' => 'Le numéro de téléphone doit être de 9 chiffres et commencer par 70, 75, 76, 77 ou 78.',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'statut.in' => 'Le statut doit être soit "active" soit "bloqué".',
            'role.in' => 'Le rôle doit être soit "admin" soit "vigile".'
        ]);


        $adminVigile->update($validatedData);

        return response()->json($adminVigile);
    }

    /**
     * Supprimer un Admin/Vigile
     */
    public function destroy($id): JsonResponse
    {
        try {
            $adminVigile = AdminVigile::findOrFail($id);
            $adminVigile->delete();
    
            return response()->json(['message' => 'Utilisateur supprimé avec succès']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Utilisateur introuvable'], 404);
        }
    }

    /**
     * Bloquer un Admin/Vigile
     */
    public function bloquer($id): JsonResponse
    {
        try {
            $adminVigile = AdminVigile::findOrFail($id);

            if ($adminVigile->statut === 'active') {
                $adminVigile->statut = 'bloqué';
                $adminVigile->save();

                return response()->json(['message' => 'Utilisateur bloqué avec succès']);
            } else {
                return response()->json(['message' => 'Utilisateur déjà bloqué'], 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Utilisateur introuvable'], 404);
        }
    }

    /**
     * Bloquer un Admin/Vigile
     */
    public function debloquer($id): JsonResponse
    {
        try {
            $adminVigile = AdminVigile::findOrFail($id);

            if ($adminVigile->statut === 'bloqué') {
                $adminVigile->statut = 'active';
                $adminVigile->save();

                return response()->json(['message' => 'Utilisateur debloqué avec succès']);
            } else {
                return response()->json(['message' => 'Utilisateur déjà actif'], 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Utilisateur introuvable'], 404);
        }
    }

    /**
     * Changer le mot de passe d'un Admin/Vigile
     */
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
            $adminVigile = AdminVigile::findOrFail($id);
            $adminVigile->mot_de_passe = $validatedData['nouveau_password'];
            $adminVigile->save();

            return response()->json(['message' => 'Mot de passe changé avec succès']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Utilisateur introuvable'], 404);
        }
    }
}
