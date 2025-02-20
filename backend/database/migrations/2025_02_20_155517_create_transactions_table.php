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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée
            $table->date('date'); // Date obligatoire
            $table->integer('montant'); // Montant obligatoire
            $table->enum('type', ['dépot', 'petit déjeuner', 'déjeuner']); // Type obligatoire
            $table->enum('operateur', ['wave', 'orange', 'free'])->nullable(); // Opérateur
            $table->unsignedBigInteger('id_etudiant'); // Clé étrangère obligatoire
            $table->foreign('id_etudiant')->references('id')->on('etudiants')->onDelete('cascade'); // Relation avec etudiants
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
