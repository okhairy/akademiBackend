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
use Illuminate\Support\Str;
use App\Mail\BienvenueEmail;
use Illuminate\Support\Facades\Mail;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use Illuminate\Support\Facades\DB;
use App\Mail\ResetPasswordMail;

class AuthController extends Controller
{
    //route qui compte le nombre toral
    public function getNombreEtudiants(): JsonResponse
{
    $nombreEtudiants = Etudiant::count(); // Récupère le nombre total d'étudiants
    return response()->json(['nombre_etudiants' => $nombreEtudiants], 200);
}

    // Inscription d'un étudiant
    public function registerEtudiant(Request $request)
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
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
                'unique:etudiants,email',
                function ($attribute, $value, $fail) {
                    if (AdminVigile::where('email', $value)->exists()) {
                        $fail('Cet email est déjà utilisé par un administrateur.');
                    }
                },
            ],
            'telephone' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($phoneUtil) {
                    try {
                        $number = $phoneUtil->parse($value, null); // Détection automatique du pays
                        if (!$phoneUtil->isValidNumber($number)) {
                            $fail("Le numéro de téléphone n'est pas valide.");
                        }
                    } catch (\Exception $e) {
                        $fail("Format du numéro invalide.");
                    }
                },
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
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'chambre.regex' => 'La chambre ne doit pas commencer par un espace, contenir deux espaces consécutifs, et ne doit contenir que des chiffres et des lettres.',
            'numero_de_dossier.required' => 'Le numéro de dossier est obligatoire.',
            'numero_de_dossier.integer' => 'Le numéro de dossier doit être un entier.',
            'numero_de_dossier.unique' => 'Ce numéro de dossier est déjà utilisé.',
        ]);

        // Générer un mot de passe fort
        $password = Str::random(8);  // Génération d'un mot de passe de 8 caractères

        $etudiant = Etudiant::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'chambre' => $request->chambre,
            'numero_de_dossier' => $request->numero_de_dossier,
            'statut' => 'active', // Statut par défaut
            'mot_de_passe' => $password,
        ]);

         // Données pour l'email
         $details = [
            'nom' => $etudiant->nom,
            'prenom' => $etudiant->prenom,
            'email' => $etudiant->email,
            'mot_de_passe' => $password // Affichage en clair pour l'email
        ];

        // Envoi de l'email de bienvenue
        Mail::to($etudiant->email)->send(new BienvenueEmail($details));

        return response()->json(['message' => 'Étudiant créé avec succès', 'etudiant' => $etudiant], 201);
    }

    // Mettre à jour un étudiant
    public function updateEtudiant(Request $request, $id)
    {
        try {
            $phoneUtil = PhoneNumberUtil::getInstance();
            
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
                    function ($attribute, $value, $fail) use ($phoneUtil) {
                        try {
                            $number = $phoneUtil->parse($value, null); // Détection automatique du pays
                            if (!$phoneUtil->isValidNumber($number)) {
                                $fail("Le numéro de téléphone n'est pas valide.");
                            }
                        } catch (\Exception $e) {
                            $fail("Format du numéro invalide.");
                        }
                    },
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

        if (!AdminVigile::where('email', $request->email)->exists() && !Etudiant::where('email', $request->email)->exists()) {
            return response()->json(['error' => 'Email non valide'], 401);
        }

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
        return response()->json(['error' => 'Mot de passe incorrect'], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Deconnexion reussie']);
    }


    public function depot(Request $request, $id): JsonResponse
    {
        $request->validate([
            'montant' => [
            'required',
            'integer',
            'min:50',
            function ($attribute, $value, $fail) {
                if ($value % 50 !== 0) {
                    $fail('Le montant doit être un multiple de 50.');
                }
            },
        ],
            'operateur' => 'required|in:wave,orange,free',
        ], [
            'montant.required' => 'Le montant est obligatoire.',
            'montant.integer' => 'Le montant doit être un entier.',
            'montant.min' => 'Le montant doit être au moins 50.',
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
            'uid_carte.required' => 'Le numéro carte est obligatoire.',
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

            if (($currentHour == 6 && $currentMinute >= 0) || ($currentHour == 11 && $currentMinute <= 30) || ($currentHour > 9 && $currentHour < 11)) {
                $montant = 50;
                $type = 'petit déjeuner';
            
            } else {
                $montant = 100;
                $type = 'déjeuner';
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

            return response()->json(['message' => 'Retrait effectué avec succès', 'transaction' => $transaction, 'etudiant' => $etudiant], 201);
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
                return response()->json([
                    'message' => 'Carte bloquée',
                    'etudiant' => $etudiant
                ], 403);
            }

            // Vérifier si la carte n'est pas bloquée
            if ($etudiant->status_carte === 'bloqué') {
                return response()->json([
                    'message' => 'Carte bloquée',
                    'etudiant' => $etudiant
                ], 200);
            }

            return response()->json([
                'message' => 'Accès autorisé',
                'etudiant' => $etudiant
            ], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Carte invalide'], 404);
        }
    }

    public function getTransactions(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
            'token' => $request->bearerToken(),
        ]);
        // $etudiant = $request->user();


        // if (!$etudiant) {
        //     return response()->json(['message' => 'Utilisateur non connecté'], 401);
        // }

        // // Vérifier si l'utilisateur est un étudiant
        // // if (!isset($etudiant->uid_carte)) {
        // //     return response()->json(['message' => 'Accès refusé : Utilisateur non autorisé'], 403);
        // // }

        // $transactions = Transaction::where('id_etudiant', $etudiant->id)->get();

        // if ($transactions->isEmpty()) {
        //     return response()->json(['message' => 'Aucune transaction pour le moment'], 200);
        // }

        // return response()->json(['transactions' => $transactions], 200);
    }

    public function test (Request $request): JsonResponse
    {
        $etudiant = $request->user();


        if (!$etudiant) {
            return response()->json(['message' => 'Utilisateur non connecté'], 401);
        }

        // Vérifier si l'utilisateur est un étudiant
        // if (!isset($etudiant->uid_carte)) {
        //     return response()->json(['message' => 'Accès refusé : Utilisateur non autorisé'], 403);
        // }

        $transactions = Transaction::where('id_etudiant', $etudiant->id)->get();

        if ($transactions->isEmpty()) {
            return response()->json(['message' => 'Aucune transaction pour le moment'], 200);
        }

        return response()->json(['transactions' => $transactions], 200);
    }

    public function getAllTransactions(Request $request): JsonResponse
    {
        $user = $request->user(); // Récupérer l'utilisateur connecté

        // Vérifier si l'utilisateur est un administrateur
       /*  if (!$user || $user->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        } */
        
        $transactions = Transaction::all();

        if ($transactions->isEmpty()) {
            return response()->json(['message' => 'Aucune transaction pour le moment'], 200);
        }

        return response()->json(['transactions' => $transactions], 200);
    }


    public function getWeeklyExpenses(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non connecté'], 401);
        }

        //Vérifier si l'utilisateur est un étudiant
        if (!isset($user->uid_carte)) {
            return response()->json(['message' => 'Accès refusé : Utilisateur non autorisé'], 403);
        }

        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();

        $transactions = Transaction::where('id_etudiant', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $expenses = [];

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $dailyTransactions = $transactions->where('date', $date->toDateString());

            $petitDejeunerCount = $dailyTransactions->where('type', 'petit déjeuner')->count();
            $dejeunerDinerCount = $dailyTransactions->whereIn('type', ['déjeuner', 'dîner'])->count();

            $expenses[] = [
                'date' => $date->toDateString(),
                'petit_dejeuner' => $petitDejeunerCount,
                'dejeuner_diner' => $dejeunerDinerCount,
            ];
        }

        return response()->json(['expenses' => $expenses], 200);
    }

    public function getMonthlyExpenses(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non connecté'], 401);
        }

        // Vérifier si l'utilisateur est un étudiant
        // if (!isset($user->uid_carte)) {
        //     return response()->json(['message' => 'Accès refusé : Utilisateur non autorisé'], 403);
        // }

            $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $expenses = [];

        for ($month = 1; $month <= $currentMonth; $month++) {
            $startDate = Carbon::create($currentYear, $month, 1)->startOfMonth();
            $endDate = Carbon::create($currentYear, $month, 1)->endOfMonth();

            $transactions = Transaction::where('id_etudiant', $user->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            $petitDejeunerCount = $transactions->where('type', 'petit déjeuner')->count();
            $dejeunerDinerCount = $transactions->whereIn('type', ['déjeuner', 'dîner'])->count();

            $expenses[] = [
                'mois' => $startDate->format('F'),
                'petit_dejeuner' => $petitDejeunerCount,
                'dejeuner_diner' => $dejeunerDinerCount,
            ];
        }

        return response()->json(['expenses' => $expenses], 200);
    }

    public function getLastDepositAndWeeklyExpenses(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non connecté'], 401);
        }

        // Vérifier si l'utilisateur est un étudiant
        if (!isset($user->uid_carte)) {
            return response()->json(['message' => 'Accès refusé : Utilisateur non autorisé'], 403);
        }

        // Récupérer le dernier dépôt
        $lastDeposit = Transaction::where('id_etudiant', $user->id)
            ->where('type', 'dépot')
            ->orderBy('date', 'desc')
            ->value('montant');

        // Calculer la somme des dépenses de la semaine en cours
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();

        $weeklyExpenses = Transaction::where('id_etudiant', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->whereIn('type', ['petit déjeuner', 'déjeuner', 'dîner'])
            ->sum('montant');

        return response()->json([
            'Dernier_depot' => $lastDeposit,
            'Depenses_dans_la_semaine' => $weeklyExpenses,
        ], 200);
    }
     /**
     * Assigner une carte RFID à un étudiant
     */
  
    /**
     * Assigner une carte à un étudiant.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignerCarte(Request $request, $id)
{
    // Validation des données
    $request->validate([
        'uid_carte' => 'required|string|unique:etudiants,uid_carte,NULL,id', // Validation personnalisée pour exclure l'étudiant en cours
    ]);

    // Trouver l'étudiant par son ID
    $etudiant = Etudiant::find($id);

    // Vérifier si l'étudiant existe
    if (!$etudiant) {
        return response()->json(['message' => 'Étudiant non trouvé'], 404);
    }

    // Vérifier si la carte est déjà assignée à un autre étudiant
    $existingStudent = Etudiant::where('uid_carte', $request->uid_carte)->first();

    if ($existingStudent) {
        // Si un autre étudiant a déjà cette carte
        return response()->json(['message' => 'Cette carte est déjà assignée à un autre étudiant'], 422);
    }

    // Mettre à jour l'UID de la carte et le statut
    $etudiant->update([
        'uid_carte' => $request->uid_carte,
        'status_carte' => 'débloqué',
    ]);

    // Réponse JSON en cas de succès
    return response()->json([
        'message' => 'Carte assignée avec succès',
        'etudiant' => $etudiant
    ], 200);
}

    
     /**
     * Désassigner une carte d'un étudiant.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function desassignerCarte($id)
    {
        // Trouver l'étudiant par son ID
        $etudiant = Etudiant::find($id);

        // Vérifier si l'étudiant existe
        if (!$etudiant) {
            return response()->json(['message' => 'Étudiant non trouvé'], 404);
        }

        // Vérifier si l'étudiant a déjà une carte assignée
        if (!$etudiant->uid_carte) {
            return response()->json(['message' => 'Aucune carte assignée à cet étudiant'], 400);
        }

        // Réinitialiser l'UID de la carte et le statut
        $etudiant->update([
            'uid_carte' => null,
            'status_carte' => null,
        ]);

        // Réponse JSON en cas de succès
        return response()->json([
            'message' => 'Carte désassignée avec succès',
            'etudiant' => $etudiant
        ], 200);
    }
    /**
     * Mettre à jour la photo d'un étudiant.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePhoto(Request $request): JsonResponse
    {
        $etudiant = $request->user();

        if (!$etudiant) {
            return response()->json(['message' => 'Utilisateur non connecté'], 401);
        }

        // Vérifier si l'utilisateur est un étudiant
        if (!isset($etudiant->uid_carte)) {
            return response()->json(['message' => 'Accès refusé : Utilisateur non autorisé'], 403);
        }
        
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'photo.required' => 'La photo est obligatoire.',
            'photo.image' => 'Le fichier doit être une image.',
            'photo.mimes' => 'La photo doit être un fichier de type: jpeg, png, jpg, gif.',
            'photo.max' => 'La photo ne doit pas dépasser 2 Mo.',
        ]);

        try {
            // Stocker la nouvelle photo
            $photoPath = $request->file('photo')->store('photos', 'public');

            // Mettre à jour le chemin de la photo dans la base de données
            $etudiant->photo = $photoPath;
            $etudiant->save();

            return response()->json(['message' => 'Photo mise à jour avec succès', 'photo' => $photoPath], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Étudiant introuvable'], 404);
        }
    }

    /**
     * Bloquer la carte d'un étudiant.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bloquerCarte(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non connecté'], 401);
        }

        // Vérifier si l'utilisateur est un étudiant
        if (!isset($user->uid_carte)) {
            return response()->json(['message' => 'Accès refusé : Utilisateur non autorisé'], 403);
        }

        try {
            $etudiant = Etudiant::findOrFail($user->id);

            // Vérifier si la carte est déjà bloquée
            if ($etudiant->status_carte === 'bloqué') {
                return response()->json(['message' => 'Carte déjà bloquée'], 200);
            }

            // Bloquer la carte
            $etudiant->status_carte = 'bloqué';
            $etudiant->save();

            return response()->json(['message' => 'Carte bloquée avec succès'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Étudiant introuvable'], 404);
        }
    }

    /**
     * Débloquer la carte d'un étudiant.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function debloquerCarte(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non connecté'], 401);
        }

        // Vérifier si l'utilisateur est un étudiant
        if (!isset($user->uid_carte)) {
            return response()->json(['message' => 'Accès refusé : Utilisateur non autorisé'], 403);
        }

        try {
            $etudiant = Etudiant::findOrFail($user->id);

            // Vérifier si la carte est déjà débloquée
            if ($etudiant->status_carte === 'débloqué') {
                return response()->json(['message' => 'Carte déjà débloquée'], 200);
            }

            // Débloquer la carte
            $etudiant->status_carte = 'débloqué';
            $etudiant->save();

            return response()->json(['message' => 'Carte débloquée avec succès'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Étudiant introuvable'], 404);
        }
    }

    /**
     * Récupérer un étudiant par son ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
 
       public function getEtudiantById(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        
        if (!$user) {
            return response()->json(['message' => 'Utilisateur non connecté'], 401);
        }

        
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        try {
            $etudiant = Etudiant::findOrFail($id);
            return response()->json(['etudiant' => $etudiant], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Étudiant introuvable'], 404);
        }
    } 
   
    
    /**
     * Récupérer tous les étudiants.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllEtudiants(Request $request): JsonResponse
    {
        $user = $request->user();

        // Vérifier si l'utilisateur est authentifié
        if (!$user) {
            return response()->json(['message' => 'Utilisateur non connecté'], 401);
        }

        // Vérifier si l'utilisateur a le droit d'accéder à ces informations (ex: admin)
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $etudiants = Etudiant::all();
        return response()->json(['etudiants' => $etudiants], 200);
    }


    /**
     * Changer le mot de passe de l'utilisateur connecté.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePwd(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non connecté'], 401);
        }

        $validatedData = $request->validate([
            'ancien_password' => 'required|string',
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
            'ancien_password.required' => 'L\'ancien mot de passe est obligatoire.',
            'nouveau_password.required' => 'Le nouveau mot de passe est obligatoire.',
            'nouveau_password.min' => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
            'nouveau_password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'nouveau_password.regex' => 'Le nouveau mot de passe doit contenir au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.',
        ]);

        // Vérifier si l'ancien mot de passe est correct
        if (!Hash::check($validatedData['ancien_password'], $user->mot_de_passe)) {
            return response()->json(['message' => 'L\'ancien mot de passe est incorrect'], 400);
        }

        // Mettre à jour le mot de passe
        $user->mot_de_passe = Hash::make($validatedData['nouveau_password']);
        $user->save();

        return response()->json(['message' => 'Mot de passe changé avec succès'], 200);
    }


    /**
     * Supprimer plusieurs étudiants.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function supprimerPlusieursEtudiants(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:etudiants,id',
        ], [
            'ids.required' => 'Les IDs des étudiants sont obligatoires.',
            'ids.array' => 'Les IDs doivent être un tableau.',
            'ids.*.integer' => 'Chaque ID doit être un entier.',
            'ids.*.exists' => 'Chaque ID doit exister dans la base de données.',
        ]);

        $ids = $request->input('ids');

        Etudiant::whereIn('id', $ids)->delete();

        return response()->json(['message' => 'Étudiants supprimés avec succès'], 200);
    }
    public function getAllUsers(): JsonResponse
    {
        $adminsVigiles = AdminVigile::all()->map(function ($user) {
            return [
                'id' => $user->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'role' => $user->role, // ou 'Admin' selon ton modèle
                'date' => $user->date_de_creation,
                "statut"=> $user->statut,
                'assignation' => 'N/A',
                
                'selected' => false
            ];
        });
    
        $etudiants = Etudiant::all()->map(function ($etudiant) {
            return [
                'id' => $etudiant->id,
                'nom' => $etudiant->nom,
                'prenom' => $etudiant->prenom,
                'email' => $etudiant->email,
                'role' => 'Etudiant',
                'date' => $etudiant->date_de_creation,
                'assignation' => $etudiant->uid_carte ? 'Assigné' : 'Désassigné', // Vérifie si une carte est assignée
                "statut"=> $etudiant->statut,
                'selected' => false
            ];
        });
    
        // Fusionner toutes les listes
        $users = $adminsVigiles->merge($etudiants);
    
        return response()->json(['users' => $users], 200);
    }
    

        /**
         * Mettre à jour les informations de l'utilisateur connecté.
         *
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */

        public function updateUserInfo(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non connecté'], 401);
        }

        $phoneUtil = PhoneNumberUtil::getInstance();

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
                'unique:etudiants,email,' . $user->id,
                function ($attribute, $value, $fail) {
                    if (AdminVigile::where('email', $value)->exists()) {
                        $fail('Cet email est déjà utilisé par un administrateur.');
                    }
                },
            ],
            'telephone' => [
                'sometimes',
                'string',
                function ($attribute, $value, $fail) use ($phoneUtil) {
                    try {
                        $number = $phoneUtil->parse($value, null); // Détection automatique du pays
                        if (!$phoneUtil->isValidNumber($number)) {
                            $fail("Le numéro de téléphone n'est pas valide.");
                        }
                    } catch (\Exception $e) {
                        $fail("Format du numéro invalide.");
                    }
                },
                'unique:etudiants,telephone,' . $user->id
            ],
        ], [
            'nom.regex' => 'Le nom ne doit pas commencer par un espace, contenir deux espaces consécutifs, et ne doit contenir que des chiffres et des lettres.',
            'prenom.regex' => 'Le prénom ne doit pas commencer par un espace, contenir deux espaces consécutifs, et ne doit contenir que des chiffres et des lettres.',
            'email.email' => 'L\'email doit être une adresse email valide.',
            'email.regex' => 'Le format de l\'email est incorrect.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'chambre.regex' => 'La chambre ne doit pas commencer par un espace, contenir deux espaces consécutifs, et ne doit contenir que des chiffres et des lettres.',
        ]);

        $user->update($request->all());

        return response()->json(['message' => 'Informations mises à jour avec succès', 'user' => $user], 200);
    }

    // Méthode pour demander la réinitialisation du mot de passe
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être une adresse email valide.',
        ]);

        $email = $request->email;

        // Vérifier si l'email existe dans l'une des deux tables
        $etudiantExists = Etudiant::where('email', $email)->exists();
        $adminVigileExists = AdminVigile::where('email', $email)->exists();

        if (!$etudiantExists && !$adminVigileExists) {
            return response()->json(['error' => 'Cet email n\'est pas valide'], 404);
        }

        $token = Str::random(60);

        // Supprimer les anciens tokens pour cet email
        DB::table('password_resets')->where('email', $email)->delete();

        // Insérer le nouveau token
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        // Envoyer l'email de réinitialisation
        Mail::to($email)->send(new ResetPasswordMail($token));

        return response()->json(['message' => 'Email de réinitialisation envoyé avec succès.'], 200);
    }

    // Méthode pour réinitialiser le mot de passe
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'password' => [
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
            'token.required' => 'Le token est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'password.regex' => 'Le mot de passe doit contenir au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.',
        ]);

        $tokenData = DB::table('password_resets')->where('token', $request->token)->first();

        if (!$tokenData) {
            return response()->json(['message' => 'Token invalide ou expiré.'], 400);
        }

        $email = $tokenData->email;

        $user = Etudiant::where('email', $email)->first();
        if (!$user) {
            $user = AdminVigile::where('email', $email)->first();
        }

        if (!$user) {
            return response()->json(['message' => 'Utilisateur introuvable.'], 404);
        }

        $user->mot_de_passe = Hash::make($request->password);
        $user->save();

        // Supprimer le token après utilisation
        DB::table('password_resets')->where('email', $email)->delete();

        return response()->json(['message' => 'Mot de passe réinitialisé avec succès.'], 200);
    }

    //modiication des utilsateurs (etudiant,admin ou vigile)
    public function updateUser(Request $request, $id): JsonResponse
    {
        try {
            $phoneUtil = PhoneNumberUtil::getInstance();
            $user = null;
            $role = $request->input('role'); 

            // Déterminer la table de l'utilisateur
            if (in_array($role, ['admin', 'vigile'])) {
                $user = AdminVigile::findOrFail($id);
            } elseif ($role === 'etudiant') {
                $user = Etudiant::findOrFail($id);
            } else {
                return response()->json(['message' => 'Rôle invalide'], 400);
            }

            // Définir les règles de validation dynamiquement
            $rules = [
                'nom' => ['sometimes', 'string', 'regex:/^[A-Za-z0-9][A-Za-z0-9 ]*$/', 'regex:/^(?!.*  ).*$/'],
                'prenom' => ['sometimes', 'string', 'regex:/^[A-Za-z0-9][A-Za-z0-9 ]*$/', 'regex:/^(?!.*  ).*$/'],
                'email' => 'sometimes|email|unique:' . ($role === 'etudiant' ? 'etudiants' : 'admin_vigiles') . ',email,' . $id,
                'telephone' => [
                    'sometimes',
                    'string',
                    // function ($attribute, $value, $fail) use ($phoneUtil) {
                    //     try {
                    //         $number = $phoneUtil->parse($value, null);
                    //         if (!$phoneUtil->isValidNumber($number)) {
                    //             $fail("Le numéro de téléphone n'est pas valide.");
                    //         }
                    //     } catch (\Exception $e) {
                    //         $fail("Format du numéro invalide.");
                    //     }
                    // },
                    'unique:' . ($role === 'etudiant' ? 'etudiants' : 'admin_vigiles') . ',telephone,' . $id
                ],
            ];

            // Ajouter des règles spécifiques selon le rôle
            if ($role === 'etudiant') {
                $rules['numero_de_dossier'] = 'sometimes|integer|unique:etudiants,numero_de_dossier,' . $id;
                $rules['chambre'] = ['nullable', 'string', 'regex:/^[A-Za-z0-9][A-Za-z0-9 ]*$/', 'regex:/^(?!.*  ).*$/'];
            }

            if ($role === 'vigile') {
                $rules['lieu'] = 'sometimes|string';
            }

            // Validation des données
            $validatedData = $request->validate($rules, [
                'nom.regex' => 'Le nom ne doit pas commencer par un espace ou contenir deux espaces consécutifs.',
                'prenom.regex' => 'Le prénom ne doit pas commencer par un espace ou contenir deux espaces consécutifs.',
                'email.email' => 'L\'email doit être une adresse email valide.',
                'email.unique' => 'Cet email est déjà utilisé.',
                'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
                'numero_de_dossier.integer' => 'Le numéro de dossier doit être un entier.',
                'numero_de_dossier.unique' => 'Ce numéro de dossier est déjà utilisé.',
                'chambre.regex' => 'La chambre ne doit pas commencer par un espace ou contenir deux espaces consécutifs.',
            ]);

            // Mise à jour de l'utilisateur
            $user->update($validatedData);

            return response()->json(['message' => ucfirst($role) . ' mis à jour avec succès', 'user' => $user], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => ucfirst($role) . ' introuvable'], 404);
        }
    }

    /**
     * Récupérer un utilisateur par son ID et son rôle.
     *
     * @param int $id
     * @param string $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserById($id, $role): JsonResponse
    {
        try {
            if ($role === 'Etudiant') {
                // Recherche dans la table des étudiants
                $etudiant = Etudiant::findOrFail($id);

                return response()->json([
                    'id' => $etudiant->id,
                    'nom' => $etudiant->nom,
                    'prenom' => $etudiant->prenom,
                    'email' => $etudiant->email,
                    'telephone' => $etudiant->telephone ?? null,
                    'role' => 'etudiant',
                    'numero_de_dossier' => $etudiant->numero_de_dossier,
                    'date' => $etudiant->date_de_creation,
                    'assignation' => $etudiant->uid_carte ? 'Assigné' : 'Désassigné',
                ], 200);
            } elseif (in_array($role, ['admin', 'vigile'])) {
                // Recherche dans la table des admins/vigiles
                $adminVigile = AdminVigile::findOrFail($id);

                return response()->json([
                    'id' => $adminVigile->id,
                    'nom' => $adminVigile->nom,
                    'prenom' => $adminVigile->prenom,
                    'email' => $adminVigile->email,
                    'telephone' => $adminVigile->telephone ?? null,
                    'role' => $adminVigile->role,
                    'date' => $adminVigile->date_de_creation,
                    'assignation' => 'N/A',
                ], 200);
            } else {
                return response()->json(['message' => 'Rôle invalide'], 400);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => ucfirst($role) . ' introuvable'], 404);
        }
    }

    public function bloquer(Request $request, $id = null): JsonResponse
    {
        $user = $request->user();

        // Vérifier si l'utilisateur est connecté
      /*   if (!$user) {
            return response()->json(['message' => 'Utilisateur non connecté'], 401);
        } */

        // Si un ID est fourni, bloquer un admin/vigile
        if ($id !== null) {
            return $this->bloquerAdminVigile($id);
        }

        // Sinon, bloquer la carte de l'étudiant connecté
        return $this->bloquerCarteEtudiant($user);
    }

    /**
     * Bloquer la carte d'un étudiant.
     *
     * @param mixed $user (Utilisateur connecté)
     * @return JsonResponse
     */
    private function bloquerCarteEtudiant($user): JsonResponse
    {
        // Vérifier si l'utilisateur est un étudiant
        if (!isset($user->uid_carte)) {
            return response()->json(['message' => 'Accès refusé : Utilisateur non autorisé'], 403);
        }

        try {
            $etudiant = Etudiant::findOrFail($user->id);

            // Vérifier si la carte est déjà bloquée
            if ($etudiant->status_carte === 'bloqué') {
                return response()->json(['message' => 'Carte déjà bloquée'], 200);
            }

            // Bloquer la carte
            $etudiant->status_carte = 'bloqué';
            $etudiant->save();

            return response()->json(['message' => 'Carte bloquée avec succès'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Étudiant introuvable'], 404);
        }
    }

    /**
     * Bloquer un admin ou un vigile.
     *
     * @param int $id (ID de l'admin/vigile)
     * @return JsonResponse
     */
    private function bloquerAdminVigile($id): JsonResponse
    {
        try {
            $adminVigile = AdminVigile::findOrFail($id);

            // Vérifier si l'utilisateur est déjà bloqué
            if ($adminVigile->statut === 'bloqué') {
                return response()->json(['message' => 'Utilisateur déjà bloqué'], 200);
            }

            // Bloquer l'utilisateur
            $adminVigile->statut = 'bloqué';
            $adminVigile->save();

            return response()->json(['message' => 'Utilisateur bloqué avec succès'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Utilisateur introuvable'], 404);
        }
    }
    public function supprimerPlusieursUtilisateurs(Request $request, string $role): JsonResponse
{
    $request->validate([
        'ids' => 'required|array',
        'ids.*' => 'integer',
    ], [
        'ids.required' => 'Les IDs des utilisateurs sont obligatoires.',
        'ids.array' => 'Les IDs doivent être un tableau.',
        'ids.*.integer' => 'Chaque ID doit être un entier.',
    ]);

    $model = null;

    if ($role === 'etudiant') {
        $model = Etudiant::class;
    } elseif ($role === 'admin_vigile') {
        $model = AdminVigile::class;
    } else {
        return response()->json(['message' => 'Type d\'utilisateur invalide.'], 400);
    }

    $ids = $request->input('ids');

    // Vérification si les IDs existent avant suppression
    if ($model::whereIn('id', $ids)->count() !== count($ids)) {
        return response()->json(['message' => 'Un ou plusieurs IDs sont invalides.'], 400);
    }

    $model::whereIn('id', $ids)->delete();

    return response()->json(['message' => ucfirst($role) . ' supprimés avec succès'], 200);
}


    public function register(Request $request): JsonResponse
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

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
            'email' => 'required|string|email|unique:etudiants,email|unique:admin_vigiles,email',
             'telephone' => [
                'required',
                'string',
                'regex:/^(70|75|76|77|78)[0-9]{7}$/', // 9 chiffres, commençant par 70, 75, 76, 77 ou 78
                'unique:etudiants,telephone',
                'unique:admin_vigiles,telephone'
            ],

            'chambre' => 'nullable|string|regex:/^[A-Za-z0-9][A-Za-z0-9 ]*$/|regex:/^(?!.*  ).*$/',
            'numero_de_dossier' => 'nullable|integer|unique:etudiants,numero_de_dossier',
            'role' => 'required|in:etudiant,admin,vigile',
            'statut' => 'in:active,bloqué',
            'lieu' => [
                'nullable',
                'required_if:role,vigile',
                'in:campus,restaurant'
            ],
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être une adresse email valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.regex' => 'Le numéro de téléphone doit commencer par 70, 75, 76, 77 ou 78 et contenir 9 chiffres au total.',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'chambre.regex' => 'La chambre ne doit pas commencer par un espace ou contenir deux espaces consécutifs.',
            'numero_de_dossier.integer' => 'Le numéro de dossier doit être un entier.',
            'numero_de_dossier.unique' => 'Ce numéro de dossier est déjà utilisé.',

        ], [
            'role.required' => 'Le rôle est obligatoire.',
            'role.in' => 'Le rôle doit être soit "etudiant", "admin" ou "vigile".'
        ]);

        // Générer un mot de passe fort
        $password = Str::random(8);

        if ($request->role === 'etudiant') {
            $user = Etudiant::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'chambre' => $request->chambre,
                'numero_de_dossier' => $request->numero_de_dossier,
                'statut' => 'active',
                'mot_de_passe' => Hash::make($password),

            ]);
        } else {
            $data = [
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'mot_de_passe' => Hash::make($password),
                'statut' => 'active',
                'role' => $request->role,
                'date_de_creation' => now(),
            ];
        
            // Ajout de 'lieu' uniquement si c'est un vigile
            if ($request->role === 'vigile') {
                $data['lieu'] = $request->lieu;
            }
        
            $user = AdminVigile::create($data);
        }


        // Données pour l'email
        $details = [
            'nom' => $user->nom,
            'prenom' => $user->prenom,
            'email' => $user->email,
            'mot_de_passe' => $password
        ];

        // Envoi de l'email de bienvenue
        Mail::to($user->email)->send(new BienvenueEmail($details));

        return response()->json(['message' => 'Utilisateur créé avec succès', 'utilisateur' => $user], 201);
        
    }

    
   
        public function supprimerUtilisateur($id, $role): JsonResponse
        {
            try {
                if ($role === 'etudiant') {
                    $utilisateur = Etudiant::findOrFail($id);
                } elseif (in_array($role, ['admin', 'vigile'])) {
                    $utilisateur = AdminVigile::findOrFail($id);
                } else {
                    return response()->json(['message' => 'Type d\'utilisateur invalide'], 400);
                }
    
                $utilisateur->delete();
                return response()->json(['message' => ucfirst($role) . ' supprimé avec succès'], 200);

                
            } catch (ModelNotFoundException $e) {
                return response()->json(['message' => ucfirst($role) . ' introuvable'], 404);
            }

            
        }
        public function recevoirData(Request $request)
        {
            // Récupérer les données envoyées par Node.js
            $rfid = $request->input('rfid');
    
            // Traiter les données (par exemple, rechercher l'utilisateur en fonction du RFID)
            // Cela dépend de la structure de ton application Laravel, mais voici un exemple :
            $utilisateur = User::where('rfid', $rfid)->first();
    
            if ($utilisateur) {
                return response()->json([
                    'message' => 'Utilisateur trouvé.',
                    'utilisateur' => $utilisateur,
                ]);
            } else {
                return response()->json([
                    'message' => 'Utilisateur non trouvé.',
                ]);
            }
        }
    
    
    
    
          
    public function getMonthlyMeals(): JsonResponse
    {
        $currentYear = Carbon::now()->year;
        $meals = [];

        for ($month = 1; $month <= 12; $month++) {
            $startDate = Carbon::create($currentYear, $month, 1)->startOfMonth();
            $endDate = Carbon::create($currentYear, $month, 1)->endOfMonth();

            $transactions = Transaction::whereBetween('date', [$startDate, $endDate])->get();

            $petitDejeunerCount = $transactions->where('type', 'petit déjeuner')->count();
            $dejeunerCount = $transactions->where('type', 'déjeuner')->count();
            $dinerCount = $transactions->where('type', 'dîner')->count();

            if ($month > Carbon::now()->month) {
                break;
            }

            $totalMeals = $petitDejeunerCount + $dejeunerCount + $dinerCount;

            $meals[] = [
                'mois' => $startDate->format('F'),
                'depenses' => $totalMeals,
            ];
        }

        return response()->json(['meals' => $meals], 200);
    }

    public function getDailyMeals(): JsonResponse
    {
        $today = Carbon::today();

        $transactions = Transaction::whereDate('date', $today)->get();

        $petitDejeunerCount = $transactions->where('type', 'petit déjeuner')->count();
        $dejeunerDinerCount = $transactions->whereIn('type', ['déjeuner', 'dîner'])->count();

        return response()->json([
            'date' => $today->toDateString(),
            'dejeuner_diner' => $dejeunerDinerCount,
            'petit_dejeuner' => $petitDejeunerCount,
        ], 200);
    }

    public function getDepots(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non connecté'], 401);
        }

        // Vérifier si l'utilisateur est un étudiant
        if (!isset($user->uid_carte)) {
            return response()->json(['message' => 'Accès refusé : Utilisateur non autorisé'], 403);
        }

        $depots = Transaction::where('id_etudiant', $user->id)
            ->where('type', 'dépot')
            ->get();

        if ($depots->isEmpty()) {
            return response()->json(['message' => 'Aucun dépôt trouvé'], 200);
        }

        return response()->json(['depots' => $depots], 200);
    }

    public function getTotalUsers(): JsonResponse
    {
        $nombreEtudiants = Etudiant::count();
        $nombreAdmins = AdminVigile::where('role', 'admin')->count();
        $nombreVigiles = AdminVigile::where('role', 'vigile')->count();
        $nombreTotal = $nombreEtudiants + $nombreAdmins + $nombreVigiles;

        return response()->json([
            'nombre_total_utilisateurs' => $nombreTotal,
            'nombre_admins' => $nombreAdmins,
            'nombre_vigiles' => $nombreVigiles,
        ], 200);
    }
    /* fonction pour recuprer l'utilisateur connecter */
    public function getUtilisateurConnecte(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Utilisateur non connecté'], 401);
        }

        return response()->json($user);
    }  
}