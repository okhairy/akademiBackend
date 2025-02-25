<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class Etudiant extends Authenticatable
{
    use Notifiable;
    use HasApiTokens;

    protected $table = 'etudiants'; // Nom de la table
    protected $fillable = ['nom', 'prenom', 'email', 'solde', 'telephone', 'chambre', 'photo', 'mot_de_passe', 'statut', 'numero_de_dossier', 'uid_carte', 'status_carte'];
    protected $hidden = ['mot_de_passe']; // Masquer le mot de passe dans les réponses JSON



    /**
     * Boot method pour le modèle.
     */
    protected static function boot()
    {
        parent::boot();

        // Événement "creating" pour définir le mot de passe par défaut
        static::creating(function ($etudiant) {
            $etudiant->mot_de_passe = Hash::make($etudiant->mot_de_passe);
        });
    }

    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }
}
