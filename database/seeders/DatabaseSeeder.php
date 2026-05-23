<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Note;
use App\Models\Eleve;
use App\Models\Paiement;
//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    //use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Compte gestionnaire principal
        User::create([
            'name'     => 'Directeur École',
            'email'    => 'admin@ecole.bf',
            'password' => 'password',
            'role'     => 'gestionnaire',
        ]);

        // Un enseignant de test
        $enseignant = User::create([
            'name'     => 'Jean Ouédraogo',
            'email'    => 'enseignant@ecole.bf',
            'password' => 'password',
            'role'     => 'enseignant',
        ]);

        // Classes
        $cm1 = Classe::create([
            'nom'             => 'CM1 A',
            'niveau'          => 'CM1',
            'frais_scolarite' => 45000,
            'annee_scolaire'  => '2025-2026',
            'user_id'         => $enseignant->id,
        ]);

        // Matières du CM1
        $maths = Matiere::create(['nom' => 'Mathématiques', 'coefficient' => 3, 'classe_id' => $cm1->id]);
        $francais = Matiere::create(['nom' => 'Français',       'coefficient' => 3, 'classe_id' => $cm1->id]);
        Matiere::create(['nom' => 'Sciences',        'coefficient' => 2, 'classe_id' => $cm1->id]);
        Matiere::create(['nom' => 'Histoire-Géo',    'coefficient' => 2, 'classe_id' => $cm1->id]);
        Matiere::create(['nom' => 'Sport',            'coefficient' => 1, 'classe_id' => $cm1->id]);

        // Quelques élèves
        $eleve1 = Eleve::create([
            'nom' => 'Kabore', 'prenom' => 'Aïcha',
            'date_naissance' => '2015-03-12',
            'classe_id' => $cm1->id,
        ]);

        // Un paiement de test
        Paiement::create([
            'eleve_id'      => $eleve1->id,
            'montant'       => 20000,
            'date_paiement' => now()->toDateString(),
        ]);

        // Une note de test
        Note::create([
            'eleve_id'       => $eleve1->id,
            'matiere_id'     => $maths->id,
            'note'           => 15.50,
            'trimestre'      => 1,
            'annee_scolaire' => '2025-2026',
        ]);
    }
}
