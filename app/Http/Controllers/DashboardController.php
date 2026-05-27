<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Note;
use App\Models\Eleve;
use App\Models\Paiement;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // gestion de l'affichage du tableau de bord du directeur (gestionnaire)
        if ($user->isGestionnaire()) {
            // Stats financières globales
            $stats = [
                'total_eleves'       => Eleve::count(),
                'total_classes'      => Classe::count(),
                'frais_attendus'     => Classe::all()->sum(fn($c) => $c->totalFraisAttendus()),
                'frais_collectes'    => Paiement::sum('montant'),
                'nb_impayes'         => Eleve::with(['classe','paiements'])
                                            ->get()
                                            ->filter(fn($e) => $e->resteAPayer() > 0)
                                            ->count(),
            ];
            $stats['taux_recouvrement'] = $stats['frais_attendus'] > 0
                ? round(($stats['frais_collectes'] / $stats['frais_attendus']) * 100, 1)
                : 0;

            return view('dashboard.gestionnaire', compact('stats'));
        }

        // gestion du tableau de bord d'un enseignant : ses classes et leurs élèves
        $classes = $user->classes()->with('eleves')->get();
        return view('dashboard.enseignant', compact('classes'));
    }

    public function impayes()
    {
        // Élèves ayant un reste à payer > 0, triés par montant dû décroissant
        $eleves = Eleve::with(['classe', 'paiements'])
                       ->get()
                       ->filter(fn($e) => $e->resteAPayer() > 0)
                       ->sortByDesc(fn($e) => $e->resteAPayer())
                       ->values();

        return view('dashboard.impayes', compact('eleves'));
    }
}
