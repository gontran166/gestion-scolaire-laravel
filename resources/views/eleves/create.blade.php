
@extends('layouts.app')

@section('title', 'Inscrire un élève')
@section('page-title', 'Inscrire un nouvel élève')

@section('page-action')
    <a href="{{ route('eleves.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour à la liste
    </a>
@endsection

@section('content')

{{-- enctype="multipart/form-data" : OBLIGATOIRE pour les formulaires avec upload de fichier --}}
<form method="POST" action="{{ route('eleves.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">

        {{-- ===== COLONNE GAUCHE : informations de l'élève ===== --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3 pb-0">
                    <h6 class="fw-semibold">Informations de l'élève</h6>
                </div>
                <div class="card-body">

                    <div class="row g-3">

                        {{-- Nom de famille --}}
                        <div class="col-md-6">
                            <label for="nom" class="form-label fw-semibold">
                                Nom <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                id="nom"
                                name="nom"
                                {{-- is-invalid : classe Bootstrap qui affiche le champ en rouge si erreur --}}
                                class="form-control @error('nom') is-invalid @enderror"
                                value="{{ old('nom') }}"
                                {{-- old() : repopule le champ après une erreur de validation --}}
                                placeholder="Ex: KABORE"
                                required
                            >
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Prénom --}}
                        <div class="col-md-6">
                            <label for="prenom" class="form-label fw-semibold">
                                Prénom <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                id="prenom"
                                name="prenom"
                                class="form-control @error('prenom') is-invalid @enderror"
                                value="{{ old('prenom') }}"
                                placeholder="Ex: Aïcha"
                                required
                            >
                            @error('prenom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Date de naissance --}}
                        <div class="col-md-6">
                            <label for="date_naissance" class="form-label fw-semibold">
                                Date de naissance <span class="text-danger">*</span>
                            </label>
                            <input
                                type="date"
                                id="date_naissance"
                                name="date_naissance"
                                class="form-control @error('date_naissance') is-invalid @enderror"
                                value="{{ old('date_naissance') }}"
                                {{-- max : interdit les dates futures pour une date de naissance --}}
                                max="{{ now()->toDateString() }}"
                                required
                            >
                            @error('date_naissance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Classe d'affectation --}}
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
                                {{-- $classes est passé par le controller depuis Classe::orderBy('niveau')->get() --}}
                                @foreach($classes as $classe)
                                    <option
                                        value="{{ $classe->id }}"
                                        {{-- selected si c'est la valeur soumise avant une erreur --}}
                                        {{ old('classe_id') == $classe->id ? 'selected' : '' }}
                                    >
                                        {{ $classe->nom }} — {{ number_format($classe->frais_scolarite, 0, ',', ' ') }} F/an
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

            {{-- ===== Informations du parent/tuteur ===== --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-0 pt-3 pb-0">
                    <h6 class="fw-semibold">Parent / Tuteur <small class="text-muted fw-normal">(optionnel)</small></h6>
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
                                value="{{ old('nom_parent') }}"
                                placeholder="Ex: KABORE Hamidou"
                            >
                        </div>

                        <div class="col-md-6">
                            <label for="telephone_parent" class="form-label">Téléphone</label>
                            <input
                                type="text"
                                id="telephone_parent"
                                name="telephone_parent"
                                class="form-control"
                                value="{{ old('telephone_parent') }}"
                                placeholder="Ex: +226 70 00 00 00"
                            >
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- ===== COLONNE DROITE : photo + bouton de soumission ===== --}}
        <div class="col-md-4">

            {{-- Upload de photo --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 pt-3 pb-0">
                    <h6 class="fw-semibold">Photo de l'élève</h6>
                </div>
                <div class="card-body text-center">

                    {{-- Prévisualisation de la photo avant upload --}}
                    <div id="photo-preview"
                         class="rounded-circle mx-auto mb-3 bg-light d-flex align-items-center
                                justify-content-center overflow-hidden"
                         style="width:100px; height:100px">
                        <i class="bi bi-person-circle text-muted" style="font-size:3rem"></i>
                    </div>

                    <input
                        type="file"
                        id="photo"
                        name="photo"
                        class="form-control @error('photo') is-invalid @enderror"
                        accept="image/*"
                        {{-- Déclenchement de la prévisualisation JS ci-dessous --}}
                        onchange="previewPhoto(this)"
                    >
                    <small class="text-muted d-block mt-1">JPG, PNG — max 2 Mo</small>

                    @error('photo')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Bouton de soumission --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                        <i class="bi bi-person-check me-2"></i> Inscrire l'élève
                    </button>
                    <a href="{{ route('eleves.index') }}" class="btn btn-light w-100 mt-2">
                        Annuler
                    </a>
                </div>
            </div>

        </div>
    </div>

</form>

@endsection

@push('scripts')
<script>
    /**
     * Prévisualise la photo sélectionnée avant upload
     * @param {HTMLInputElement} input - le champ file
     */
    function previewPhoto(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            const preview = document.getElementById('photo-preview');

            reader.onload = function(e) {
                // Remplace l'icône par l'image sélectionnée
                preview.innerHTML = `<img src="${e.target.result}"
                    style="width:100px; height:100px; object-fit:cover"
                    class="rounded-circle">`;
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush