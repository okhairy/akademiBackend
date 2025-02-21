<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;



class AdminVigile extends Authenticatable
{
    use HasFactory;

    protected $table = 'admin_vigiles'; // Nom de la table

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'mot_de_passe',
        'statut',
        'role',
        'date_de_creation'
    ];

    protected $hidden = [
        'mot_de_passe',
    ];

    public $timestamps = false; // Désactive les timestamps

    /**
     * Mutateur pour hasher le mot de passe avant sauvegarde
     */
    public function setMotDePasseAttribute($value)
    {
        $this->attributes['mot_de_passe'] = Hash::make($value);
    }

    /**
     * Boot method pour le modèle.
     */
    protected static function boot()
    {
        parent::boot();

        // Événement "creating" pour définir le mot de passe par défaut
        static::creating(function ($adminVigile) {
            if (empty($adminVigile->mot_de_passe)) {
                $adminVigile->mot_de_passe = 'passer123';
            }
            $adminVigile->mot_de_passe = Hash::make($adminVigile->mot_de_passe);
        });
    }
  
  
}
