{{-- resources/views/notes/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Notes')
@section('page-title', 'Notes')

@section('content')

{{-- Filtre : classe / trimestre / année --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('notes.index') }}" class="row g-3 align-items-end">

            <div class="col-md-4">
                <label class="form-label fw-semibold">Classe</label>
                <select name="classe_id" class="form-select" onchange="this.form.submit()">
                    <option value="">— Sélectionner une classe —</option>
                    @foreach($classes as $classe)
                        <option value="{{ $classe->id }}"
                            {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                            {{ $classe->nom }} ({{ $classe->niveau }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">Trimestre</label>
                <select name="trimestre" class="form-select" onchange="this.form.submit()">
                    <option value="1" {{ request('trimestre', 1) == 1 ? 'selected' : '' }}>1er trimestre</option>
                    <option value="2" {{ request('trimestre') == 2 ? 'selected' : '' }}>2ème trimestre</option>
                    <option value="3" {{ request('trimestre') == 3 ? 'selected' : '' }}>3ème trimestre</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">Année scolaire</label>
                <input type="text"
                       name="annee_scolaire"
                       class="form-control"
                       value="{{ request('annee_scolaire', '2025-2026') }}"
                       placeholder="2025-2026">
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i> Filtrer
                </button>
            </div>

        </form>
    </div>
</div>

{{-- Grille des notes --}}
@if($classeSelectionnee)

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
        <div class="d-flex gap-2 align-items-center">

            {{-- Badge indiquant le mode courant (lecture ou saisie) --}}
            @if($peutSaisir)
                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                    <i class="bi bi-pencil me-1"></i> Mode saisie
                </span>
            @else
                <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2">
                    <i class="bi bi-eye me-1"></i> Lecture seule
                </span>
            @endif

            <a href="{{ route('notes.classement', [
                    'classe'         => $classeSelectionnee->id,
                    'trimestre'      => request('trimestre', 1),
                    'annee_scolaire' => request('annee_scolaire', '2025-2026'),
                ]) }}"
               class="btn btn-sm btn-outline-primary">
                <i class="bi bi-trophy me-1"></i> Classement
            </a>
        </div>
    </div>

    @if($classeSelectionnee->matieres->isEmpty())
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Aucune matière configurée pour cette classe.
        </div>

    @elseif($classeSelectionnee->eleves->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Aucun élève inscrit dans cette classe.
        </div>

    @else

        {{-- Bandeau d'information pour le gestionnaire --}}
        @if(!$peutSaisir)
            <div class="alert alert-info d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-info-circle-fill fs-5 flex-shrink-0"></i>
                <div>
                    <strong>Consultation uniquement.</strong>
                    Seul l'enseignant responsable de cette classe peut saisir ou modifier les notes.
                    @if($classeSelectionnee->enseignant)
                        Enseignant responsable :
                        <strong>{{ $classeSelectionnee->enseignant->name }}</strong>.
                    @endif
                </div>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead>
                            <tr class="table-light">
                                <th style="min-width:180px; position:sticky; left:0; background:#f8f9fa; z-index:2">
                                    Élève
                                </th>
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
                                <td class="fw-semibold"
                                    style="position:sticky; left:0; background:#fff; z-index:1">
                                    {{ $eleve->nom_complet }}
                                </td>

                                @foreach($classeSelectionnee->matieres as $matiere)
                                @php
                                    $noteExistante = $eleve->notes
                                        ->firstWhere('matiere_id', $matiere->id);
                                    $valeur = $noteExistante?->note ?? null;
                                    $surCombien = $matiere->coefficient === 1 ? 10 : 20;
                                @endphp

                                <td class="p-1 text-center">

                                    {{-- ========================================
                                         SAISIE : uniquement pour les enseignants
                                         ======================================== --}}
                                    @if($peutSaisir)

                                        <form method="POST"
                                              action="{{ route('notes.store') }}"
                                              class="note-form">
                                            @csrf
                                            <input type="hidden" name="eleve_id"       value="{{ $eleve->id }}">
                                            <input type="hidden" name="matiere_id"     value="{{ $matiere->id }}">
                                            <input type="hidden" name="trimestre"      value="{{ request('trimestre', 1) }}">
                                            <input type="hidden" name="annee_scolaire" value="{{ request('annee_scolaire', '2025-2026') }}">
                                            <div class="input-group input-group-sm" style="min-width:100px">
                                                <input
                                                    type="number"
                                                    name="note"
                                                    class="form-control form-control-sm text-center note-input
                                                        @if($valeur !== null)
                                                            {{ $valeur >= ($surCombien / 2) ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}
                                                        @endif"
                                                    value="{{ $valeur ?? '' }}"
                                                    min="0" max="{{ $surCombien }}" step="0.25"
                                                    placeholder="—"
                                                    onblur="submitNoteForm(this)"
                                                    onkeydown="if(event.key==='Enter'){event.preventDefault(); this.blur();}"
                                                >
                                                <span class="input-group-text px-1 text-muted"
                                                      style="font-size:0.7rem">/{{ $surCombien }}</span>
                                            </div>

                                            <div class="save-indicator text-success mt-1"
                                                 style="font-size:0.65rem; display:none">
                                                <i class="bi bi-check-circle"></i> Enregistré
                                            </div>
                                        </form>

                                    {{-- ========================================
                                         LECTURE : uniquement pour le gestionnaire
                                         ======================================== --}}
                                    @else

                                        @if($valeur !== null)
                                            {{-- Note affichée en badge coloré, non éditable --}}
                                            <span class="badge fs-6 px-3
                                                {{ $valeur >= ($surCombien / 2) ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}">
                                                {{ number_format($valeur, 2) }}
                                            </span>
                                            <div class="text-muted" style="font-size:0.65rem">/{{$surCombien}}</div>
                                        @else
                                            {{-- Pas encore de note saisie --}}
                                            <span class="text-muted" style="font-size:0.85rem">—</span>
                                        @endif

                                    @endif

                                </td>
                                @endforeach

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-white border-0 pb-3">
                <div class="d-flex gap-3 small text-muted flex-wrap">

                    {{-- Message différent selon le rôle --}}
                    <span class="ms-auto">
                        @if($peutSaisir)
                            <i class="bi bi-info-circle me-1"></i>
                            Notes enregistrées automatiquement à la sortie du champ.
                        @else
                            <i class="bi bi-lock me-1"></i>
                            Consultation uniquement — modification réservée à l'enseignant.
                        @endif
                    </span>

                </div>
            </div>
        </div>

    @endif

@else
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-journal-text text-muted opacity-25" style="font-size:3rem"></i>
            <p class="text-muted mt-3 mb-0">
                Sélectionnez une classe et un trimestre pour afficher les notes.
            </p>
        </div>
    </div>
@endif

@endsection

@push('scripts')
{{-- Le script fetch n'est chargé que si l'utilisateur peut saisir des notes --}}
@if($peutSaisir)
<script>
    function submitNoteForm(input) {
        if (input.value === '') return;

        const val = parseFloat(input.value);
        const max = parseFloat(input.max);
        if (isNaN(val) || val < 0 || val > max) {
            input.classList.add('is-invalid');
            return;
        }
        input.classList.remove('is-invalid');

        const form      = input.closest('form');
        const indicator = form.querySelector('.save-indicator');

        input.style.opacity = '0.5';

        fetch(form.action, {
            method:  'POST',
            body:    new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(response => {
            input.style.opacity = '1';

            if (response.ok) {
                input.classList.remove(
                    'bg-success', 'bg-danger',
                    'bg-opacity-10', 'text-success', 'text-danger'
                );
                if (val >= (max / 2)) {
                    input.classList.add('bg-success', 'bg-opacity-10', 'text-success');
                } else {
                    input.classList.add('bg-danger', 'bg-opacity-10', 'text-danger');
                }
                indicator.style.display = 'block';
                setTimeout(() => { indicator.style.display = 'none'; }, 2000);
            } else {
                input.classList.add('is-invalid');
            }
        })
        .catch(() => {
            input.style.opacity = '1';
            input.classList.add('is-invalid');
        });
    }
</script>
@endif
@endpush