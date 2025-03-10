<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Transaction extends Model
{
    use HasFactory;
    use HasApiTokens;

    protected $fillable = [
        'date',
        'montant',
        'type',
        'operateur',
        'id_etudiant',
    ];

    /**
     * Relation avec le modèle Etudiant.
     */
    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class, 'id_etudiant');
    }

    public $timestamps = false; // Désactive les timestamps
}
