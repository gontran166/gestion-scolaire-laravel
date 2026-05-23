@extends('layouts.app')

@section('title', 'Modifier une classe')
@section('page-title', 'Modifier — ' . $classe->nom)

@section('page-action')
    <a href="{{ route('classes.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour aux classes
    </a>
@endsection

@section('content')

<div class="row justify-content-center">
<div class="col-md-7">

<form method="POST" action="{{ route('classes.update', $classe) }}">
    @csrf
    @method('PUT')

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-3 pb-0">
            <h6 class="fw-semibold">Modifier les informations de la classe</h6>
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
                        {{-- old() en 2e argument : valeur de secours si pas d'erreur de validation --}}
                        value="{{ old('nom', $classe->nom) }}"
                        required
                    >
                    @error('nom')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

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
                        @foreach(['CP1','CP2','CE1','CE2','CM1','CM2'] as $niveau)
                            <option value="{{ $niveau }}"
                                {{ old('niveau', $classe->niveau) === $niveau ? 'selected' : '' }}>
                                {{ $niveau }}
                            </option>
                        @endforeach
                    </select>
                    @error('niveau')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="frais_scolarite" class="form-label fw-semibold">
                        Frais annuels (F CFA) <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input
                            type="number"
                            id="frais_scolarite"
                            name="frais_scolarite"
                            class="form-control @error('frais_scolarite') is-invalid @enderror"
                            value="{{ old('frais_scolarite', $classe->frais_scolarite) }}"
                            min="0"
                            step="500"
                            required
                        >
                        <span class="input-group-text">F CFA</span>
                    </div>
                    {{-- Avertissement si des paiements existent déjà --}}
                    @if($classe->eleves_count > 0)
                        <div class="form-text text-warning">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Modifier ce montant recalculera le reste à payer pour les
                            {{ $classe->eleves_count }} élèves de cette classe.
                        </div>
                    @endif
                    @error('frais_scolarite')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="annee_scolaire" class="form-label fw-semibold">
                        Année scolaire <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="annee_scolaire"
                        name="annee_scolaire"
                        class="form-control @error('annee_scolaire') is-invalid @enderror"
                        value="{{ old('annee_scolaire', $classe->annee_scolaire) }}"
                        required
                    >
                    @error('annee_scolaire')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="user_id" class="form-label fw-semibold">
                        Enseignant responsable
                    </label>
                    <select
                        id="user_id"
                        name="user_id"
                        class="form-select @error('user_id') is-invalid @enderror"
                    >
                        <option value="">— Aucun —</option>
                        @foreach($enseignants as $enseignant)
                            <option value="{{ $enseignant->id }}"
                                {{ old('user_id', $classe->user_id) == $enseignant->id ? 'selected' : '' }}>
                                {{ $enseignant->name }}
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
                <i class="bi bi-floppy me-2"></i> Enregistrer
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