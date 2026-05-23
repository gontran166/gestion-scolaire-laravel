<?php

namespace App\Services;

use App\Models\Paiement;

class PdfReceptService
{
    /**
     * Génère un reçu PDF pour un paiement et le sauvegarde dans le storage.
     * Retourne le chemin relatif du fichier créé.
     *
     * On utilise DomPDF (intégré via barryvdh/laravel-dompdf)
     * Installation : composer require barryvdh/laravel-dompdf
     */
    public function generate(Paiement $paiement): string
    {
        // Chargement de l'élève avec sa classe pour afficher les infos complètes
        $paiement->load(['eleve.classe']);
        $eleve = $paiement->eleve;

        // Génération du HTML du reçu via une vue Blade dédiée
        $html = view('pdf.recu_paiement', [
            'paiement'      => $paiement,
            'eleve'         => $eleve,
            'classe'        => $eleve->classe,
            'total_paye'    => $eleve->totalPaye(),
            'reste_a_payer' => $eleve->resteAPayer(),
        ])->render();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        $pdf->setPaper('A5', 'portrait');

        // Stockage dans storage/app/public/recus/
        $filename = "recus/recu_{$paiement->id}_" . now()->format('Ymd') . ".pdf";
        \Storage::disk('public')->put($filename, $pdf->output());

        return $filename;
    }
}