<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EnseignantController extends Controller
{
    public function index()
    {
        // Chargement de tous les enseignants avec leurs classes associées
        // withCount('classes') ajoute un attribut classes_count sur chaque user
        $enseignants = User::where('role', 'enseignant')
                           ->with('classes')
                           ->withCount('classes')
                           ->orderBy('name')
                           ->get();

        // On passe aussi les classes sans enseignant pour le widget d'affectation rapide
        $classesLibres = Classe::whereNull('user_id')
                               ->orderBy('niveau')
                               ->get();

        return view('enseignants.index', compact('enseignants', 'classesLibres'));
    }

    public function create()
    {
        // Toutes les classes disponibles pour l'affectation au moment de la création
        $classes = Classe::with('enseignant')->orderBy('niveau')->get();
        return view('enseignants.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'unique:users,email'],
            // Password::defaults() : règle Laravel qui exige min 8 chars
            'password'   => ['required', 'confirmed', Password::defaults()],
            // classe_id est optionnel : on peut créer un enseignant sans classe
            'classe_id'  => ['nullable', 'exists:classes,id'],
        ]);

        // Création de l'enseignant avec le rôle forcé à 'enseignant'
        // On ne laisse pas le formulaire décider du rôle pour éviter
        // qu'un utilisateur malveillant se crée un compte gestionnaire
        $enseignant = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'enseignant', // forcé, jamais depuis le formulaire
        ]);

        // Si une classe a été sélectionnée, on l'affecte immédiatement
        if (!empty($data['classe_id'])) {
            Classe::where('id', $data['classe_id'])
                  ->update(['user_id' => $enseignant->id]);
        }

        return redirect()->route('enseignants.index')
                         ->with('success', "L'enseignant {$enseignant->name} a été ajouté avec succès.");
    }

    public function edit(User $enseignant)
    {
        // Sécurité : on s'assure qu'on édite bien un enseignant, pas un gestionnaire
        abort_if($enseignant->role !== 'enseignant', 403);

        $classes = Classe::with('enseignant')->orderBy('niveau')->get();
        return view('enseignants.edit', compact('enseignant', 'classes'));
    }

    public function update(Request $request, User $enseignant)
    {
        abort_if($enseignant->role !== 'enseignant', 403);

        $data = $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            // unique sauf pour cet utilisateur lui-même (ignore:users,id)
            'email'     => ['required', 'email', "unique:users,email,{$enseignant->id}"],
            // Mot de passe optionnel à la modification : on ne change que s'il est fourni
            'password'  => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $enseignant->update([
            'name'  => $data['name'],
            'email' => $data['email'],
            // Si un nouveau mot de passe est fourni on le hash, sinon on garde l'ancien
            ...(!empty($data['password'])
                ? ['password' => $data['password']]
                : []),
        ]);

        return redirect()->route('enseignants.index')
                         ->with('success', 'Informations mises à jour.');
    }

    public function destroy(User $enseignant)
    {
        abort_if($enseignant->role !== 'enseignant', 403);

        // Avant de supprimer : on libère les classes qu'il gérait
        // (user_id mis à null, les classes ne sont pas supprimées)
        Classe::where('user_id', $enseignant->id)
              ->update(['user_id' => null]);

        $enseignant->delete();

        return redirect()->route('enseignants.index')
                         ->with('success', 'Enseignant supprimé. Ses classes sont désormais sans responsable.');
    }

    /**
     * Affectation rapide d'une classe à un enseignant depuis la liste.
     * Appelée via un formulaire Ajax-like dans la vue index.
     */
    public function affecterClasse(Request $request, User $enseignant)
    {
        abort_if($enseignant->role !== 'enseignant', 403);

        $data = $request->validate([
            'classe_id' => ['required', 'exists:classes,id'],
        ]);

        // Si la classe était déjà affectée à quelqu'un d'autre, on la libère d'abord
        $classe = Classe::findOrFail($data['classe_id']);

        if ($classe->user_id && $classe->user_id !== $enseignant->id) {
            // On informe que la classe change de main
            $ancienEnseignant = User::find($classe->user_id)?->name ?? 'un autre enseignant';
            $message = "La classe {$classe->nom} a été retirée à {$ancienEnseignant} et affectée à {$enseignant->name}.";
        } else {
            $message = "La classe {$classe->nom} a été affectée à {$enseignant->name}.";
        }

        $classe->update(['user_id' => $enseignant->id]);

        return redirect()->route('enseignants.index')->with('success', $message);
    }
}
