<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Note;
use App\Models\Eleve;
use App\Models\Paiement;
use App\Services\MoyenneService;

class NoteController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // L'enseignant ne voit que ses classes, le gestionnaire voit tout
        $classes = $user->isGestionnaire()
            ? Classe::with('matieres')->get()
            : $user->classes()->with('matieres')->get();

        // Initialisation à null : pas de classe sélectionnée par défaut
        $classeSelectionnee = null;

        // Si une classe est choisie dans le filtre (paramètre GET ?classe_id=X)
        if (request('classe_id')) {

            // On cherche la classe avec ses matières ET ses élèves
            // Les élèves sont chargés avec leurs notes filtrées par trimestre et année
            // pour pré-remplir la grille sans requête supplémentaire dans la vue
            $classeSelectionnee = Classe::with([
                'matieres',
                // Eager loading des élèves triés alphabétiquement
                'eleves' => fn($q) => $q->orderBy('nom')->orderBy('prenom'),
                // Pour chaque élève, on charge uniquement ses notes du trimestre/année sélectionnés
                'eleves.notes' => fn($q) => $q
                    ->where('trimestre', request('trimestre', 1))
                    ->where('annee_scolaire', request('annee_scolaire', '2025-2026')),
            ])->find(request('classe_id'));

            // Sécurité : un enseignant ne peut pas accéder à une classe qui n'est pas la sienne
            if (!$user->isGestionnaire()) {
                // On vérifie que la classe demandée appartient bien à cet enseignant
                $classeAutorisee = $user->classes->contains('id', request('classe_id'));
                if (!$classeAutorisee) {
                    abort(403, 'Cette classe ne vous est pas assignée.');
                }
            }
        }

        return view('notes.index', compact('classes', 'classeSelectionnee'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'eleve_id'       => ['required', 'exists:eleves,id'],
            'matiere_id'     => ['required', 'exists:matieres,id'],
            'note'           => ['required', 'numeric', 'min:0', 'max:20'],
            'trimestre'      => ['required', 'integer', 'in:1,2,3'],
            'annee_scolaire' => ['required', 'string'],
        ]);

        // updateOrCreate : met à jour si la note existe déjà, sinon crée
        // Clé de recherche : les 4 premiers champs (unicité définie dans la migration)
        // Valeur à mettre à jour : uniquement 'note'
        Note::updateOrCreate(
            [
                'eleve_id'       => $data['eleve_id'],
                'matiere_id'     => $data['matiere_id'],
                'trimestre'      => $data['trimestre'],
                'annee_scolaire' => $data['annee_scolaire'],
            ],
            ['note' => $data['note']]
        );

        // Réponse différente selon le type de requête :
        // - fetch (Ajax) : on retourne juste un JSON succès (pas de redirection)
        // - formulaire classique : on redirige
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Note enregistrée.');
    }

    public function classement(Classe $classe)
    {
        $trimestre = request('trimestre', 1);
        $annee = request('annee_scolaire', '2025-2026');

        // Calcul des moyennes via le service dédié
        $classement = app(MoyenneService::class)
                        ->calculerClassement($classe, $trimestre, $annee);

        return view('notes.classement', compact('classe', 'classement', 'trimestre', 'annee'));
    }
}
