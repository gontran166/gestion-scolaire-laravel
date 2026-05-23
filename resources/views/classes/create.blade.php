
{{-- Création d'une nouvelle classe avec ses frais et son enseignant responsable --}}

@extends('layouts.app')

@section('title', 'Créer une classe')
@section('page-title', 'Créer une nouvelle classe')

@section('page-action')
    <a href="{{ route('classes.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour aux classes
    </a>
@endsection

@section('content')

<div class="row justify-content-center">
<div class="col-md-7">

<form method="POST" action="{{ route('classes.store') }}">
    @csrf

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-3 pb-0">
            <h6 class="fw-semibold">Informations de la classe</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">

                {{-- Nom de la classe --}}
                <div class="col-md-6">
                    <label for="nom" class="form-label fw-semibold">
                        Nom de la classe <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="nom"
                        name="nom"
                        class="form-control @error('nom') is-invalid @enderror"
                        value="{{ old('nom') }}"
                        placeholder="Ex: CM1 A"
                        required
                    >
                    <div class="form-text">
                        Nom affiché dans l'application (ex: CP1 A, CE2 B)
                    </div>
                    @error('nom')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Niveau officiel --}}
                <div class="col-md-6">
                    <label for="niveau" class="form-label fw-semibold">
                        Niveau <span class="text-danger">*</span>
                    </label>
                    <select
                        id="niveau"
                        name="niveau"
                        class="form-select @error('niveau') is-invalid @enderror"
                        required
                    >
                        <option value="">— Choisir —</option>
                        {{-- Les niveaux correspondent à l'enum défini dans la migration --}}
                        @foreach(['CP1','CP2','CE1','CE2','CM1','CM2'] as $niveau)
                            <option value="{{ $niveau }}"
                                {{ old('niveau') === $niveau ? 'selected' : '' }}>
                                {{ $niveau }}
                            </option>
                        @endforeach
                    </select>
                    @error('niveau')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Frais de scolarité annuels --}}
                <div class="col-md-6">
                    <label for="frais_scolarite" class="form-label fw-semibold">
                        Frais de scolarité annuels (F CFA) <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input
                            type="number"
                            id="frais_scolarite"
                            name="frais_scolarite"
                            class="form-control @error('frais_scolarite') is-invalid @enderror"
                            value="{{ old('frais_scolarite') }}"
                            min="0"
                            step="500"
                            placeholder="Ex: 45000"
                            required
                        >
                        <span class="input-group-text">F CFA</span>
                    </div>
                    <div class="form-text">
                        Montant total dû par élève pour l'année
                    </div>
                    @error('frais_scolarite')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Année scolaire --}}
                <div class="col-md-6">
                    <label for="annee_scolaire" class="form-label fw-semibold">
                        Année scolaire <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="annee_scolaire"
                        name="annee_scolaire"
                        class="form-control @error('annee_scolaire') is-invalid @enderror"
                        value="{{ old('annee_scolaire', '2025-2026') }}"
                        placeholder="2025-2026"
                        required
                    >
                    @error('annee_scolaire')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Enseignant responsable (optionnel à la création) --}}
                <div class="col-12">
                    <label for="user_id" class="form-label fw-semibold">
                        Enseignant responsable
                        <small class="text-muted fw-normal">(optionnel)</small>
                    </label>
                    <select
                        id="user_id"
                        name="user_id"
                        class="form-select @error('user_id') is-invalid @enderror"
                    >
                        <option value="">— Assigner plus tard —</option>
                        {{-- $enseignants est passé par le controller --}}
                        @foreach($enseignants as $enseignant)
                            <option value="{{ $enseignant->id }}"
                                {{ old('user_id') == $enseignant->id ? 'selected' : '' }}>
                                {{ $enseignant->name }}
                                {{-- Badge rôle pour distinguer si jamais on affiche des gestionnaires --}}
                                ({{ $enseignant->role }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>

        <div class="card-footer bg-white border-0 pb-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                <i class="bi bi-plus-circle me-2"></i> Créer la classe
            </button>
            <a href="{{ route('classes.index') }}" class="btn btn-light">
                Annuler
            </a>
        </div>
    </div>

</form>

</div>
</div>

@endsection