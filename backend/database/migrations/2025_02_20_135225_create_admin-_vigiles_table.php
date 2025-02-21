<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin_vigiles', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée
            $table->string('nom'); // Nom obligatoire
            $table->string('prenom'); // Prénom obligatoire
            $table->string('email')->unique(); // Email unique et obligatoire
            $table->string('telephone')->unique(); // Téléphone unique et obligatoire
            $table->string('mot_de_passe'); // Mot de passe chiffré
            $table->enum('statut', ['active', 'bloqué'])->default('active'); // Statut avec valeur par défaut "active"
            $table->enum('role', ['admin', 'vigile']); // Rôle admin ou vigile
            $table->timestamp('date_de_creation')->useCurrent(); // Date de création automatique
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin-_vigiles');
    }
};
