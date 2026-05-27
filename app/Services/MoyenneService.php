<?php
namespace App\Services;

use App\Models\Classe;
use App\Models\Eleve;

class MoyenneService
{
    /**
     * Calcule la moyenne pondérée d'un élève pour un trimestre donné.
     * Formule : Σ(note × coeff) ÷ Σ(coeff)
     */
    public function calculerMoyenne(Eleve $eleve, int $trimestre, string $annee): ?float
    {
        // On charge les notes avec la matière (pour le coefficient)
        $notes = $eleve->notes()
                       ->with('matiere')
                       ->where('trimestre', $trimestre)
                       ->where('annee_scolaire', $annee)
                       ->get();

        if ($notes->isEmpty()) {
            return null;
        }

        $totalNote  = $notes->sum(fn($n) => $n->note * $n->matiere->coefficient);
        $totalcoef = $notes->sum(fn($n) => $n->matiere->coefficient);

        return $totalcoef > 0
            ? round($totalNote / $totalcoef, 2)
            : null;
    }

    /**
     * Retourne le classement complet d'une classe pour un trimestre.
     * Chaque entrée contient l'élève, sa moyenne et son rang.
     */
    public function calculerClassement(Classe $classe, int $trimestre, string $annee): array
    {
        $eleves = $classe->eleves()->with(['notes.matiere'])->get();

        $resultats = $eleves->map(function (Eleve $eleve) use ($trimestre, $annee) {
            return [
                'eleve'   => $eleve,
                'moyenne' => $this->calculerMoyenne($eleve, $trimestre, $annee),
            ];
        })
        // Les élèves sans notes vont en bas du classement
        ->sortByDesc(fn($r) => $r['moyenne'] ?? -1)
        ->values()
        ->toArray();

        // Ajout du rang (les ex-aequo ont le même rang)
        $rang = 1;
        foreach ($resultats as $i => &$resultat) {
            if ($i > 0 && $resultat['moyenne'] === $resultats[$i - 1]['moyenne']) {
                $resultat['rang'] = $resultats[$i - 1]['rang']; // ex-aequo
            } else {
                $resultat['rang'] = $rang;
            }
            $rang++;
        }

        return $resultats;
    }
}