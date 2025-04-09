<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;




class AdminVigile extends Authenticatable
{
    use HasFactory;
    use HasApiTokens;
    

    protected $table = 'admin_vigiles'; // Nom de la table

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'mot_de_passe',
        'statut',
        'role',
        'lieu',
        'date_de_creation'
    ];

    protected $hidden = [
        'mot_de_passe',
    ];

    public $timestamps = false; // Désactive les timestamps


    

    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }
  
  
}
