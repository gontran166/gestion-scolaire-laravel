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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')
               ->constrained()
               ->cascadeOnDelete();
            $table->foreignId('matiere_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->decimal('note', 5, 2);                 // ex: 14.50 sur 20
            $table->unsignedTinyInteger('trimestre');       // 1, 2 ou 3
            $table->string('annee_scolaire');
            $table->timestamps();

            // suppression logique
            $table->softDeletes();

            // Un élève ne peut avoir qu'une seule note par matière par trimestre
            $table->unique(['eleve_id', 'matiere_id', 'trimestre', 'annee_scolaire']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
