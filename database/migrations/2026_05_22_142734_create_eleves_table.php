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
        Schema::create('eleves', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->date('date_naissance');
            $table->string('photo')->nullable();            // chemin vers le fichier uploadé
            $table->string('nom_parent')->nullable();
            $table->string('telephone_parent')->nullable();
            $table->foreignId('classe_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();

            # Suppression logique
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eleves');
    }
};
