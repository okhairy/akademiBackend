<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Etudiant extends Authenticatable
{
    use Notifiable;

    protected $table = 'etudiants'; // Nom de la table
    protected $fillable = ['nom', 'prenom', 'email', 'solde', 'telephone', 'chambre', 'photo', 'mot_de_passe', 'statut', 'numero_de_dossier', 'uid_carte', 'status_carte'];
    protected $hidden = ['mot_de_passe']; // Masquer le mot de passe dans les réponses JSON
}
