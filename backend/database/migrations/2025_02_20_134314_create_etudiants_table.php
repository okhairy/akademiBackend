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
        Schema::create('etudiants', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->float('solde')->default(0);
            $table->string('telephone');
            $table->string('chambre');
            $table->string('photo')->nullable();
            $table->string('mot_de_passe')->default(Hash::make('passer123')); // Mot de passe par défaut
            $table->enum('statut', ['active', 'bloqué'])->default('active');
            $table->date('date_de_creation');
            $table->integer('numero_de_dossier')->unique();
            $table->string('uid_carte')->unique()->nullable();
            $table->enum('status_carte', ['bloqué', 'débloqué'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etudiants');
    }
};
