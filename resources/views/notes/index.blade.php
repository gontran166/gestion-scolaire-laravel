@extends('layouts.app')

@section('title', 'Saisie des notes')
@section('page-title', 'Saisie des notes')

@section('content')

{{-- ===== FORMULAIRE DE FILTRE ===== --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('notes.index') }}" class="row g-3 align-items-end">

            {{-- Sélection de la classe --}}
            <div class="col-md-4">
                <label class="form-label fw-semibold">Classe</label>
                <select name="classe_id"
                        class="form-select"
                        {{-- onchange : soumet automatiquement le formulaire quand la classe change
                             évite un clic supplémentaire sur "Filtrer" --}}
                        onchange="this.form.submit()">
                    <option value="">— Sélectionner une classe —</option>
                    @foreach($classes as $classe)
                        <option value="{{ $classe->id }}"
                            {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                            {{ $classe->nom }} ({{ $classe->niveau }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Sélection du trimestre --}}
            <div class="col-md-3">
                <label class="form-label fw-semibold">Trimestre</label>
                <select name="trimestre"
                        class="form-select"
                        onchange="this.form.submit()">
                    <option value="1" {{ request('trimestre', 1) == 1 ? 'selected' : '' }}>
                        1er trimestre
                    </option>
                    <option value="2" {{ request('trimestre') == 2 ? 'selected' : '' }}>
                        2ème trimestre
                    </option>
                    <option value="3" {{ request('trimestre') == 3 ? 'selected' : '' }}>
                        3ème trimestre
                    </option>
                </select>
            </div>

            {{-- Année scolaire --}}
            <div class="col-md-3">
                <label class="form-label fw-semibold">Année scolaire</label>
                <input type="text"
                       name="annee_scolaire"
                       class="form-control"
                       value="{{ request('annee_scolaire', '2025-2026') }}"
                       placeholder="2025-2026">
            </div>

            {{-- Bouton explicite en cas de changement d'année scolaire --}}
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i> Filtrer
                </button>
            </div>

        </form>
    </div>
</div>

{{-- ===== GRILLE DE SAISIE ===== --}}
@if($classeSelectionnee)

    {{-- En-tête avec nom de la classe et lien vers le classement --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h6 class="fw-semibold mb-0">
                {{ $classeSelectionnee->nom }} —
                Trimestre {{ request('trimestre', 1) }} —
                {{ request('annee_scolaire', '2025-2026') }}
            </h6>
            <small class="text-muted">
                {{ $classeSelectionnee->eleves->count() }} élève(s) ·
                {{ $classeSelectionnee->matieres->count() }} matière(s)
            </small>
        </div>
        <a href="{{ route('notes.classement', [
                'classe'         => $classeSelectionnee->id,
                'trimestre'      => request('trimestre', 1),
                'annee_scolaire' => request('annee_scolaire', '2025-2026'),
            ]) }}"
           class="btn btn-sm btn-outline-primary">
            <i class="bi bi-trophy me-1"></i> Voir le classement
        </a>
    </div>

    {{-- Vérification qu'il y a des matières configurées pour cette classe --}}
    @if($classeSelectionnee->matieres->isEmpty())
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Aucune matière configurée</strong> pour la classe
            {{ $classeSelectionnee->nom }}.
            Le gestionnaire doit d'abord créer les matières de cette classe.
        </div>

    {{-- Vérification qu'il y a des élèves dans la classe --}}
    @elseif($classeSelectionnee->eleves->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Aucun élève inscrit dans la classe {{ $classeSelectionnee->nom }}.
        </div>

    {{-- Tout est bon : on affiche la grille --}}
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead>
                            <tr class="table-light">
                                {{-- Colonne fixe : nom de l'élève --}}
                                <th class="fw-semibold" style="min-width:180px; position:sticky; left:0; background:#f8f9fa; z-index:2">
                                    Élève
                                </th>
                                {{-- Une colonne par matière --}}
                                @foreach($classeSelectionnee->matieres as $matiere)
                                    <th class="text-center fw-semibold" style="min-width:130px">
                                        {{ $matiere->nom }}
                                        <br>
                                        <small class="fw-normal text-muted">
                                            Coeff. {{ $matiere->coefficient }}
                                        </small>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classeSelectionnee->eleves as $eleve)
                            <tr>
                                {{-- Nom de l'élève collé à gauche même lors du scroll horizontal --}}
                                <td class="fw-semibold"
                                    style="position:sticky; left:0; background:#fff; z-index:1">
                                    {{ $eleve->nom_complet }}
                                </td>

                                @foreach($classeSelectionnee->matieres as $matiere)
                                @php
                                    // Recherche de la note existante pour cet élève + cette matière
                                    // Les notes ont déjà été chargées par eager loading dans le controller
                                    // (filtrées par trimestre et annee_scolaire)
                                    // firstWhere() cherche dans la collection en mémoire : pas de requête SQL
                                    $noteExistante = $eleve->notes
                                        ->firstWhere('matiere_id', $matiere->id);
                                @endphp
                                <td class="p-1 text-center">
                                    {{-- Chaque cellule = un mini formulaire autonome.
                                         La soumission se déclenche quand le champ perd le focus (onblur).
                                         updateOrCreate dans le controller gère création ET modification. --}}
                                    <form method="POST"
                                          action="{{ route('notes.store') }}"
                                          class="note-form">
                                        @csrf
                                        {{-- Champs cachés portant le contexte de la note --}}
                                        <input type="hidden" name="eleve_id"       value="{{ $eleve->id }}">
                                        <input type="hidden" name="matiere_id"     value="{{ $matiere->id }}">
                                        <input type="hidden" name="trimestre"      value="{{ request('trimestre', 1) }}">
                                        <input type="hidden" name="annee_scolaire" value="{{ request('annee_scolaire', '2025-2026') }}">

                                        <div class="input-group input-group-sm" style="min-width:100px">
                                            <input
                                                type="number"
                                                name="note"
                                                class="form-control form-control-sm text-center note-input
                                                    {{-- Fond coloré selon la note existante :
                                                         vert si >= 10, rouge si < 10, blanc si vide --}}
                                                    @if($noteExistante)
                                                        {{ $noteExistante->note >= 10 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}
                                                    @endif"
                                                {{-- Valeur pré-remplie si note existe déjà --}}
                                                value="{{ $noteExistante?->note ?? '' }}"
                                                min="0"
                                                max="20"
                                                step="0.25"
                                                placeholder="—"
                                                {{-- Soumet quand le champ perd le focus --}}
                                                onblur="submitNoteForm(this)"
                                                {{-- Empêche la soumission sur Entrée (évite les doubles envois) --}}
                                                onkeydown="if(event.key==='Enter'){event.preventDefault(); this.blur();}"
                                            >
                                            <span class="input-group-text px-1 text-muted" style="font-size:0.7rem">
                                                /20
                                            </span>
                                        </div>

                                        {{-- Indicateur visuel de sauvegarde (affiché brièvement après envoi) --}}
                                        <div class="save-indicator text-success mt-1"
                                             style="font-size:0.65rem; display:none; min-height:12px">
                                            <i class="bi bi-check-circle"></i> Enregistré
                                        </div>

                                    </form>
                                </td>
                                @endforeach

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Légende en bas du tableau --}}
            <div class="card-footer bg-white border-0 pb-3">
                <div class="d-flex gap-3 small text-muted">
                    <span>
                        <span class="badge bg-success bg-opacity-10 text-success">
                            Note ≥ 10
                        </span> Admis
                    </span>
                    <span>
                        <span class="badge bg-danger bg-opacity-10 text-danger">
                            Note &lt; 10
                        </span> Insuffisant
                    </span>
                    <span class="ms-auto">
                        <i class="bi bi-info-circle me-1"></i>
                        Les notes sont enregistrées automatiquement à la sortie du champ.
                    </span>
                </div>
            </div>

        </div>
    @endif

@else
    {{-- Aucune classe sélectionnée : message d'invite --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-journal-text text-muted opacity-25" style="font-size:3rem"></i>
            <p class="text-muted mt-3 mb-0">
                Sélectionnez une classe et un trimestre pour afficher la grille de saisie.
            </p>
        </div>
    </div>
@endif

@endsection

@push('scripts')
<script>
    /**
     * Soumet le formulaire parent d'un champ note via fetch (sans recharger la page).
     * Affiche brièvement "Enregistré" sous le champ après succès.
     *
     * @param {HTMLInputElement} input - le champ note qui a perdu le focus
     */
    function submitNoteForm(input) {

        // Si le champ est vide on ne soumet pas (pas de note vide à enregistrer)
        if (input.value === '') return;

        // Validation côté client : entre 0 et 20
        const val = parseFloat(input.value);
        if (isNaN(val) || val < 0 || val > 20) {
            input.classList.add('is-invalid');
            return;
        }
        input.classList.remove('is-invalid');

        const form      = input.closest('form');
        const indicator = form.querySelector('.save-indicator');
        const formData  = new FormData(form);

        // Indication visuelle pendant l'envoi
        input.style.opacity = '0.5';

        // Envoi asynchrone via fetch : pas de rechargement de page
        fetch(form.action, {
            method: 'POST',
            body:   formData,
            headers: {
                // X-Requested-With identifie la requête comme Ajax pour Laravel
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        .then(response => {
            input.style.opacity = '1';

            if (response.ok) {
                // Mise à jour de la couleur du fond selon la note saisie
                input.classList.remove(
                    'bg-success', 'bg-danger', 'bg-opacity-10',
                    'text-success', 'text-danger'
                );
                if (val >= 10) {
                    input.classList.add('bg-success', 'bg-opacity-10', 'text-success');
                } else {
                    input.classList.add('bg-danger', 'bg-opacity-10', 'text-danger');
                }

                // Affichage de l'indicateur "Enregistré" pendant 2 secondes
                indicator.style.display = 'block';
                setTimeout(() => { indicator.style.display = 'none'; }, 2000);

            } else {
                // En cas d'erreur serveur : bordure rouge sur le champ
                input.classList.add('is-invalid');
                console.error('Erreur lors de la sauvegarde de la note');
            }
        })
        .catch(err => {
            // Erreur réseau
            input.style.opacity = '1';
            input.classList.add('is-invalid');
            console.error('Erreur réseau :', err);
        });
    }
</script>
@endpush