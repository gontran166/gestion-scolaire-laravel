@extends('layouts.app')

@section('title', 'Modifier un élève')
@section('page-title', 'Modifier — ' . $eleve->nom_complet)

@section('page-action')
    <a href="{{ route('eleves.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour à la liste
    </a>
@endsection

@section('content')

{{-- ============================================================
     FORMULAIRE PRINCIPAL : modification de l'élève
     ============================================================ --}}
<form method="POST"
      action="{{ route('eleves.update', $eleve) }}"
      enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">

        {{-- ===== COLONNE GAUCHE ===== --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3 pb-0">
                    <h6 class="fw-semibold">Informations de l'élève</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label for="nom" class="form-label fw-semibold">
                                Nom <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                id="nom"
                                name="nom"
                                class="form-control @error('nom') is-invalid @enderror"
                                value="{{ old('nom', $eleve->nom) }}"
                                required
                            >
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="prenom" class="form-label fw-semibold">
                                Prénom <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                id="prenom"
                                name="prenom"
                                class="form-control @error('prenom') is-invalid @enderror"
                                value="{{ old('prenom', $eleve->prenom) }}"
                                required
                            >
                            @error('prenom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="date_naissance" class="form-label fw-semibold">
                                Date de naissance <span class="text-danger">*</span>
                            </label>
                            <input
                                type="date"
                                id="date_naissance"
                                name="date_naissance"
                                class="form-control @error('date_naissance') is-invalid @enderror"
                                value="{{ old('date_naissance', $eleve->date_naissance->format('Y-m-d')) }}"
                                max="{{ now()->toDateString() }}"
                                required
                            >
                            @error('date_naissance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="classe_id" class="form-label fw-semibold">
                                Classe <span class="text-danger">*</span>
                            </label>
                            <select
                                id="classe_id"
                                name="classe_id"
                                class="form-select @error('classe_id') is-invalid @enderror"
                                required
                            >
                                <option value="">— Choisir une classe —</option>
                                @foreach($classes as $classe)
                                    <option
                                        value="{{ $classe->id }}"
                                        {{ old('classe_id', $eleve->classe_id) == $classe->id ? 'selected' : '' }}
                                    >
                                        {{ $classe->nom }} —
                                        {{ number_format($classe->frais_scolarite, 0, ',', ' ') }} F/an
                                    </option>
                                @endforeach
                            </select>
                            @error('classe_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            {{-- Informations du parent --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-0 pt-3 pb-0">
                    <h6 class="fw-semibold">
                        Parent / Tuteur
                        <small class="text-muted fw-normal">(optionnel)</small>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label for="nom_parent" class="form-label">Nom du parent</label>
                            <input
                                type="text"
                                id="nom_parent"
                                name="nom_parent"
                                class="form-control"
                                value="{{ old('nom_parent', $eleve->nom_parent) }}"
                            >
                        </div>

                        <div class="col-md-6">
                            <label for="telephone_parent" class="form-label">Téléphone</label>
                            <input
                                type="text"
                                id="telephone_parent"
                                name="telephone_parent"
                                class="form-control"
                                value="{{ old('telephone_parent', $eleve->telephone_parent) }}"
                            >
                        </div>

                    </div>
                </div>
            </div>

            {{-- Récapitulatif financier (lecture seule) --}}
            <div class="card border-0 shadow-sm mt-4
                {{ $eleve->resteAPayer() > 0 ? 'border-start border-warning border-3' : 'border-start border-success border-3' }}">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">Situation financière actuelle</h6>
                    <div class="row text-center g-3">

                        <div class="col-4">
                            <div class="text-muted small">Frais annuels</div>
                            <div class="fw-bold">
                                {{ number_format($eleve->classe->frais_scolarite, 0, ',', ' ') }} F
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="text-muted small">Total payé</div>
                            <div class="fw-bold text-success">
                                {{ number_format($eleve->totalPaye(), 0, ',', ' ') }} F
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="text-muted small">Reste à payer</div>
                            <div class="fw-bold {{ $eleve->resteAPayer() > 0 ? 'text-danger' : 'text-success' }}">
                                @if($eleve->resteAPayer() <= 0)
                                    <i class="bi bi-check-circle"></i> Soldé
                                @else
                                    {{ number_format($eleve->resteAPayer(), 0, ',', ' ') }} F
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        {{-- ===== COLONNE DROITE : photo + bouton enregistrer ===== --}}
        <div class="col-md-4">

            {{-- Photo --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 pt-3 pb-0">
                    <h6 class="fw-semibold">Photo de l'élève</h6>
                </div>
                <div class="card-body text-center">

                    <div id="photo-preview"
                         class="rounded-circle mx-auto mb-3 overflow-hidden
                                d-flex align-items-center justify-content-center"
                         style="width:100px; height:100px; background:#f0f0f0">
                        @if($eleve->photo)
                            <img src="{{ Storage::url($eleve->photo) }}"
                                 alt="{{ $eleve->nom_complet }}"
                                 style="width:100px; height:100px; object-fit:cover">
                        @else
                            <span class="text-primary fw-bold" style="font-size:2rem">
                                {{ strtoupper(substr($eleve->prenom, 0, 1) . substr($eleve->nom, 0, 1)) }}
                            </span>
                        @endif
                    </div>

                    @if($eleve->photo)
                        <p class="text-muted small mb-2">
                            <i class="bi bi-check-circle text-success me-1"></i>
                            Photo actuelle conservée si aucune nouvelle sélectionnée
                        </p>
                    @endif

                    <input
                        type="file"
                        id="photo"
                        name="photo"
                        class="form-control @error('photo') is-invalid @enderror"
                        accept="image/*"
                        onchange="previewPhoto(this)"
                    >
                    <small class="text-muted d-block mt-1">JPG, PNG — max 2 Mo</small>

                    @error('photo')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror

                </div>
            </div>

            {{-- Bouton enregistrer --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    {{-- Ce bouton soumet le formulaire principal (PUT) --}}
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                        <i class="bi bi-floppy me-2"></i> Enregistrer les modifications
                    </button>
                    <a href="{{ route('eleves.index') }}" class="btn btn-light w-100 mt-2">
                        Annuler
                    </a>
                </div>
            </div>

        </div>
    </div>

</form>


{{-- ============================================================
     FORMULAIRE : suppression de l'élève
     ============================================================ --}}
<div class="row mt-4">
    <div class="col-md-8">
        <div class="card border border-danger">
            <div class="card-header bg-danger bg-opacity-10 border-0 pt-3 pb-0">
                <h6 class="fw-semibold text-danger">
                    <i class="bi bi-exclamation-triangle me-1"></i> Attention
                </h6>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-3">
                    La suppression est irréversible. Tous les paiements
                    et notes associés seront également supprimés.
                </p>

                <form method="POST"
                      action="{{ route('eleves.destroy', $eleve) }}"
                      onsubmit="return confirm('Supprimer définitivement {{ $eleve->nom_complet }} ? Cette action est irréversible.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-trash me-2"></i> Supprimer cet élève
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function previewPhoto(input) {
        if (input.files && input.files[0]) {
            const reader  = new FileReader();
            const preview = document.getElementById('photo-preview');
            reader.onload = function(e) {
                preview.innerHTML = `
                    <img src="${e.target.result}"
                         style="width:100px; height:100px; object-fit:cover"
                         class="rounded-circle">
                `;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush