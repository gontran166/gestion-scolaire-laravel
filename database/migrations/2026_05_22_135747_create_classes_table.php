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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();                      // ex: "CP1 A", "CM2 B"
            $table->enum('niveau', ['CP1','CP2','CE1','CE2','CM1','CM2']);
            $table->decimal('frais_scolarite', 10, 2);      // montant total dû par an
            $table->string('annee_scolaire');               // ex: "2025-2026"
            $table->foreignId('user_id')                    // l'enseignant responsable
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->timestamps();
            
            #suppression logique
            $table->softDeletes();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
