<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Note;
use App\Models\Eleve;
use App\Models\Paiement;
use Illuminate\Support\Facades\Storage;

class EleveController extends Controller
{
    public function index()
    {
        $classeId = request('classe_id');
        $search   = request('search');

        $eleves = Eleve::with('classe')
            ->when($classeId, fn($q) => $q->where('classe_id', $classeId))
            ->when($search, fn($q) => $q->where(function($q) use ($search) {
                // Recherche dans nom OU prénom
                $q->where('nom',    'like', "%{$search}%")
                ->orWhere('prenom', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(20)
            ->withQueryString();
            // withQueryString() : conserve classe_id et search dans les liens de pagination

        $classeSelectionnee = $classeId
            ?Classe::find($classeId)
            : null;

        return view('eleves.index', compact('eleves', 'classeSelectionnee'));
    }

    public function create()
    {
        $classes = Classe::orderBy('niveau')->get();
        return view('eleves.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'               => ['required', 'string', 'max:100'],
            'prenom'            => ['required', 'string', 'max:100'],
            'date_naissance'    => ['required', 'date', 'before:today'],
            'classe_id'         => ['required', 'exists:classes,id'],
            'nom_parent'        => ['nullable', 'string', 'max:100'],
            'telephone_parent'  => ['nullable', 'string', 'max:20'],
            'photo'             => ['nullable', 'image', 'max:2048'], // 2 Mo max
        ]);

        // Gestion du chargement de la photo
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('photos/eleves', 'public');
        }

        Eleve::create($data);

        return redirect()->route('eleves.index')
                         ->with('success', "L'élève a été inscrit avec succès.");
    }

    
    public function edit(Eleve $eleve)
    {
        $classes = Classe::orderBy('niveau')->get();
        return view('eleves.edit', compact('eleve', 'classes'));
    }

    // valider la modification d'un élève
    public function update(Request $request, Eleve $eleve)
    {
        $data = $request->validate([
            'nom'               => ['required', 'string', 'max:100'],
            'prenom'            => ['required', 'string', 'max:100'],
            'date_naissance'    => ['required', 'date', 'before:today'],
            'classe_id'         => ['required', 'exists:classes,id'],
            'nom_parent'        => ['nullable', 'string', 'max:100'],
            'telephone_parent'  => ['nullable', 'string', 'max:20'],
            'photo'             => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            // Supprime l'ancienne photo avant d'en stocker une nouvelle
            if ($eleve->photo) {
                Storage::disk('public')->delete($eleve->photo);
            }
            $data['photo'] = $request->file('photo')->store('photos/eleves', 'public');
        }

        $eleve->update($data);

        return redirect()->route('eleves.index')
                         ->with('success', 'Informations mises à jour.');
    }

    public function destroy(Eleve $eleve)
    {
        if ($eleve->photo) {
            Storage::disk('public')->delete($eleve->photo);
        }
        $eleve->delete();
        return redirect()->route('eleves.index')
                         ->with('success', 'Élève supprimé.');
    }
}