<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Note;
use App\Models\Eleve;
use App\Models\Paiement;
use App\Services\PdfReceptService;

class PaiementController extends Controller
{
    public function create()
    {
        // On précharge les élèves avec leur classe et leurs paiements
        $eleves = Eleve::with(['classe', 'paiements'])->orderBy('nom')->get();
        return view('paiements.create', compact('eleves'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'eleve_id'      => ['required', 'exists:eleves,id'],
            'montant'       => ['required', 'numeric', 'min:1'],
            'date_paiement' => ['required', 'date'],
            'observations'  => ['nullable', 'string', 'max:500'],
        ]);

        // Vérification : le montant ne dépasse pas le reste à payer
        $eleve = Eleve::with(['classe', 'paiements'])->findOrFail($data['eleve_id']);
        if ($data['montant'] > $eleve->resteAPayer()) {
            return back()->withErrors([
                'montant' => "Le montant ({$data['montant']}) dépasse le reste à payer ({$eleve->resteAPayer()})."
            ])->withInput();
        }

        $paiement = Paiement::create($data);

        // Génération du reçu PDF via le service dédié
        $pdfPath = app(PdfReceptService::class)->generate($paiement);
        $paiement->update(['recu_pdf' => $pdfPath]);

        return redirect()->route('paiements.recu', $paiement)
                         ->with('success', 'Paiement enregistré. Reçu généré.');
    }

    public function recu(Paiement $paiement)
    {
        // Téléchargement du PDF depuis le storage
        $path = storage_path("app/public/{$paiement->recu_pdf}");

        if (!file_exists($path)) {
            abort(404, 'Reçu introuvable.');
        }

        return response()->download($path, "recu_paiement_{$paiement->id}.pdf");
    }
}
