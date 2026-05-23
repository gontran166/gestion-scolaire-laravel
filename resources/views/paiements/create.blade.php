
@extends('layouts.app')

@section('title', 'Enregistrer un paiement')
@section('page-title', 'Enregistrer un paiement')

@section('content')

<div class="row g-4">

    {{-- ===== FORMULAIRE DE PAIEMENT ===== --}}
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <form method="POST" action="{{ route('paiements.store') }}">
                    @csrf

                    {{-- Sélection de l'élève --}}
                    <div class="mb-4">
                        <label for="eleve_id" class="form-label fw-semibold">
                            Élève <span class="text-danger">*</span>
                        </label>
                        <select
                            id="eleve_id"
                            name="eleve_id"
                            class="form-select @error('eleve_id') is-invalid @enderror"
                            required
                            {{-- Appel JS qui met à jour le panneau d'info à droite --}}
                            onchange="updateEleveInfo(this)"
                        >
                            <option value="">— Sélectionner un élève —</option>
                            @foreach($eleves as $eleve)
                                <option
                                    value="{{ $eleve->id }}"
                                    {{-- On passe les données financières en data-attributes
                                         pour les lire côté JavaScript sans appel AJAX --}}
                                    data-frais="{{ $eleve->classe->frais_scolarite }}"
                                    data-paye="{{ $eleve->totalPaye() }}"
                                    data-reste="{{ $eleve->resteAPayer() }}"
                                    data-classe="{{ $eleve->classe->nom }}"
                                    {{ old('eleve_id') == $eleve->id ? 'selected' : '' }}
                                >
                                    {{ $eleve->nom_complet }} — {{ $eleve->classe->nom }}
                                    (reste: {{ number_format($eleve->resteAPayer(), 0, ',', ' ') }} F)
                                </option>
                            @endforeach
                        </select>
                        @error('eleve_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Montant du versement --}}
                    <div class="mb-4">
                        <label for="montant" class="form-label fw-semibold">
                            Montant versé (F CFA) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input
                                type="number"
                                id="montant"
                                name="montant"
                                class="form-control @error('montant') is-invalid @enderror"
                                value="{{ old('montant') }}"
                                min="1000"
                                step="500"
                                placeholder="Ex: 15000"
                                required
                            >
                            <span class="input-group-text">F CFA</span>
                        </div>
                        @error('montant')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Date du paiement --}}
                    <div class="mb-4">
                        <label for="date_paiement" class="form-label fw-semibold">
                            Date du versement <span class="text-danger">*</span>
                        </label>
                        <input
                            type="date"
                            id="date_paiement"
                            name="date_paiement"
                            class="form-control @error('date_paiement') is-invalid @enderror"
                            {{-- Pré-rempli avec la date d'aujourd'hui --}}
                            value="{{ old('date_paiement', now()->toDateString()) }}"
                            required
                        >
                        @error('date_paiement')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Observations optionnelles --}}
                    <div class="mb-4">
                        <label for="observations" class="form-label">Observations</label>
                        <textarea
                            id="observations"
                            name="observations"
                            class="form-control"
                            rows="2"
                            placeholder="Ex: Paiement partiel convenu avec le parent..."
                        >{{ old('observations') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-2 fw-semibold">
                        <i class="bi bi-printer me-2"></i>
                        Enregistrer et générer le reçu PDF
                    </button>
                </form>

            </div>
        </div>
    </div>

    {{-- ===== PANNEAU D'INFORMATION ÉLÈVE (mis à jour dynamiquement) ===== --}}
    <div class="col-md-5">
        <div class="card border-0 shadow-sm" id="info-card">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-semibold">Situation financière de l'élève</h6>
            </div>
            <div class="card-body" id="eleve-info">
                {{-- Ce contenu est remplacé par JS quand un élève est sélectionné --}}
                <p class="text-muted text-center py-4">
                    <i class="bi bi-arrow-left-circle me-1"></i>
                    Sélectionnez un élève pour voir sa situation
                </p>
            </div>
        </div>

        {{-- Rappel du process --}}
        <div class="card border-0 shadow-sm mt-3 border-start border-info border-3">
            <div class="card-body">
                <h6 class="fw-semibold text-info"><i class="bi bi-info-circle me-1"></i> Comment ça marche</h6>
                <ol class="small text-muted mb-0 ps-3">
                    <li>Sélectionnez l'élève</li>
                    <li>Vérifiez le reste à payer affiché</li>
                    <li>Saisissez le montant versé</li>
                    <li>Le reçu PDF sera automatiquement généré</li>
                </ol>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    /**
     * Met à jour le panneau d'info à droite quand un élève est sélectionné.
     * Lit les data-attributes de l'option choisie pour éviter un appel AJAX.
     */
    function updateEleveInfo(select) {
        const option = select.options[select.selectedIndex];
        const panel  = document.getElementById('eleve-info');

        // Si aucun élève sélectionné, réinitialise le panneau
        if (!option.value) {
            panel.innerHTML = '<p class="text-muted text-center py-4">Sélectionnez un élève</p>';
            return;
        }

        // Lecture des données financières depuis les data-attributes
        const frais  = parseFloat(option.dataset.frais);
        const paye   = parseFloat(option.dataset.paye);
        const reste  = parseFloat(option.dataset.reste);
        const classe = option.dataset.classe;

        // Calcul du pourcentage payé pour la barre de progression
        const pct = Math.round((paye / frais) * 100);

        // Couleur de la barre selon le taux
        const couleur = pct >= 100 ? 'success' : pct >= 50 ? 'warning' : 'danger';

        // Injection du HTML dans le panneau
        panel.innerHTML = `
            <div class="mb-3">
                <span class="badge bg-secondary bg-opacity-10 text-secondary">${classe}</span>
            </div>

            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted small">Frais annuels</span>
                <span class="fw-semibold">${frais.toLocaleString('fr-FR')} F</span>
            </div>

            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted small">Déjà payé</span>
                <span class="fw-semibold text-success">${paye.toLocaleString('fr-FR')} F</span>
            </div>

            <div class="progress my-2" style="height:8px">
                <div class="progress-bar bg-${couleur}" style="width:${Math.min(100,pct)}%"></div>
            </div>

            <div class="d-flex justify-content-between">
                <span class="fw-semibold">Reste à payer</span>
                <span class="fw-bold ${reste > 0 ? 'text-danger' : 'text-success'} fs-5">
                    ${reste > 0 ? reste.toLocaleString('fr-FR') + ' F' : '✓ Soldé'}
                </span>
            </div>

            ${reste > 0 ? `
            <div class="alert alert-info py-2 mt-3 mb-0 small">
                <i class="bi bi-lightbulb me-1"></i>
                Vous pouvez verser jusqu'à <strong>${reste.toLocaleString('fr-FR')} F</strong>
            </div>` : ''}
        `;

        // Pré-remplit le champ montant avec le reste à payer
        if (reste > 0) {
            document.getElementById('montant').value = reste;
        }
    }
</script>
@endpush