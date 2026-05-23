<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\User;
use App\Models\Matiere; 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClasseController extends Controller
{
    /**
     * 1. Affiche la liste de toutes les classes (actives uniquement).
     */
    public function index()
    {
        // On récupère les classes en chargeant aussi les infos de l'enseignant lié (Eager Loading)
        $classes = Classe::with(['enseignant', 'eleves.paiements'])
                 ->withCount('eleves')
                 ->get();

        return view('classes.index', compact('classes'));
    }

    /**
     * 2. Affiche le formulaire de création d'une classe.
     */
    public function create()
    {
        // On récupère uniquement les enseignants pour pouvoir les lister dans un menu déroulant (<select>)
        $enseignants = User::where('role', 'enseignant')->get();

        return view('classes.create', compact('enseignants'));
    }

    /**
     * 3. Enregistre une nouvelle classe dans la base de données.
     */
    public function store(Request $request)
    {
        // Validation stricte des données reçues du formulaire
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'niveau' => ['required', Rule::in(['CP1', 'CP2', 'CE1', 'CE2', 'CM1', 'CM2'])],
            'frais_scolarite' => 'required|numeric|min:0',
            'annee_scolaire' => 'required|string|max:9', // ex: 2025-2026 (9 caractères)
            'user_id' => 'nullable|exists:users,id', // Vérifie que l'ID de l'enseignant existe bien
        ]);

        // Création de la classe
        Classe::create($validated);

        return redirect()->route('classes.index')
            ->with('success', 'La classe a été créée avec succès !');
    }

    /**
     * 4. Affiche les détails d'une classe spécifique.
     */
    public function show(Classe $classe)
    {
        // Fiche détaillée d'une classe avec ses matières et élèves
        $classe->load(['matieres', 'eleves', 'enseignant']);
        return view('classes.show', compact('classe'));
    }

    public function storeMatieres(Request $request, Classe $classe)
    {
        $data = $request->validate([
            'nom'          => ['required', 'string', 'max:100'],
            'coefficient'  => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        // La matière est toujours liée à cette classe
        $classe->matieres()->create($data);

        return back()->with('success', "Matière \"{$data['nom']}\" ajoutée.");
    }

    public function destroyMatiere(Classe $classe, Matiere $matiere)
    {
        // Sécurité : la matière doit appartenir à cette classe
        abort_if($matiere->classe_id !== $classe->id, 403);

        $matiere->delete();

        return back()->with('success', 'Matière supprimée.');
    }

    /**
     * 5. Affiche le formulaire de modification d'une classe.
     */
    public function edit(Classe $classe)
    {
        $enseignants = User::where('role', 'enseignant')->get();

        return view('classes.edit', compact('classe', 'enseignants'));
    }

    /**
     * 6. Enregistre les modifications de la classe.
     */
    public function update(Request $request, Classe $classe)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'niveau' => ['required', Rule::in(['CP1', 'CP2', 'CE1', 'CE2', 'CM1', 'CM2'])],
            'frais_scolarite' => 'required|numeric|min:0',
            'annee_scolaire' => 'required|string|max:9',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $classe->update($validated);

        return redirect()->route('classes.index')
            ->with('success', 'La classe a été mise à jour avec succès !');
    }

    /**
     * 7. Supprime logiquement (Soft Delete) la classe.
     */
    public function destroy(Classe $classe)
    {
        // Déclenche le Soft Delete (et la cascade sur les élèves si configurée dans le modèle)
        $classe->delete();

        return redirect()->route('classes.index')
            ->with('success', 'La classe a été archivée (supprimée logiquement).');
    }
}