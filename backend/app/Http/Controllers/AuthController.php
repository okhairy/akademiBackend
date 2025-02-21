<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\AdminVigile;
use App\Models\Etudiant;

class AuthController extends Controller
{
   // Inscription d'un admin
public function registerAdmin(Request $request)
{
    // Validation des données
    $request->validate([
        'nom' => 'required|string',
        'prenom' => 'required|string',
        'email' => 'required|email|unique:admin_vigiles,email',
        'telephone' => 'required|string|unique:admin_vigiles,telephone',
        'mot_de_passe' => 'required|string|min:8',
    ], [
        'email.unique' => 'Cet email est déjà utilisé par un autre administrateur.', // Message personnalisé
        'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.', // Message personnalisé
        'mot_de_passe.min' => 'Le mot de passe doit contenir au moins 8 caractères.', // Message personnalisé
    ]);

    // Création de l'admin
    $admin = AdminVigile::create([
        'nom' => $request->nom,
        'prenom' => $request->prenom,
        'email' => $request->email,
        'telephone' => $request->telephone,
        'mot_de_passe' => Hash::make($request->mot_de_passe), // Hachage du mot de passe
        'role' => 'admin', // Rôle admin
        'statut' => 'active', // Statut par défaut
    ]);

    // Réponse JSON en cas de succès
    return response()->json([
        'message' => 'Admin créé avec succès',
        'admin' => $admin
    ], 201); // Code HTTP 201 : Created
}

    // Inscription d'un vigile
    public function registerVigile(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email|unique:admin_vigiles,email',
            'telephone' => 'required|string|unique:admin_vigiles,telephone',
            'mot_de_passe' => 'required|string|min:8',
        ]);

        $vigile = AdminVigile::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'mot_de_passe' => Hash::make($request->mot_de_passe),
            'role' => 'vigile', // Rôle vigile
            'statut' => 'active', // Statut par défaut
        ]);

        return response()->json(['message' => 'Vigile créé avec succès', 'vigile' => $vigile], 201);
    }

    // Inscription d'un étudiant
    public function registerEtudiant(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email|unique:etudiants,email',
            'telephone' => 'required|string|unique:etudiants,telephone',
            'chambre' => 'required|string',
            'numero_de_dossier' => 'required|integer|unique:etudiants,numero_de_dossier',
            'mot_de_passe' => 'required|string|min:8',
        ]);

        $etudiant = Etudiant::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'chambre' => $request->chambre,
            'numero_de_dossier' => $request->numero_de_dossier,
            'mot_de_passe' => Hash::make($request->mot_de_passe),
            'statut' => 'active', // Statut par défaut
        ]);

        return response()->json(['message' => 'Étudiant créé avec succès', 'etudiant' => $etudiant], 201);
    }
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Essayer de se connecter en tant qu'admin/vigile
        if (Auth::guard('admin_vigile')->attempt($credentials)) {
            $user = Auth::guard('admin_vigile')->user();
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
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
                'role' => 'etudiant',
            ]);
        }

        // Si l'authentification échoue
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}