@extends('layouts.app')

@section('title', $classe->nom)
@section('page-title', $classe->nom . ' — ' . $classe->niveau)

@section('page-action')
    <a href="{{ route('classes.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
@endsection

@section('content')

<div class="row g-4">

    {{-- ===== COLONNE GAUCHE : matières de la classe ===== --}}
    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-semibold">
                    <i class="bi bi-book me-2 text-primary"></i>
                    Matières
                    <span class="badge bg-primary bg-opacity-10 text-primary ms-1">
                        {{ $classe->matieres->count() }}
                    </span>
                </h6>
            </div>

            {{-- Liste des matières existantes --}}
            <div class="card-body pb-2">
                @forelse($classe->matieres as $matiere)
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2
                            {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div>
                        <span class="fw-semibold">{{ $matiere->nom }}</span>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary ms-2">
                            Coeff. {{ $matiere->coefficient }}
                        </span>
                    </div>
                    {{-- Bouton suppression de la matière --}}
                    <form method="POST"
                          action="{{ route('classes.matieres.destroy', [$classe, $matiere]) }}"
                          onsubmit="return confirm('Supprimer la matière {{ $matiere->nom }} ? Toutes ses notes seront perdues.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm text-danger p-0">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
                @empty
                <p class="text-muted small text-center py-2">
                    Aucune matière. Ajoutez-en une ci-dessous.
                </p>
                @endforelse
            </div>$classe

            {{-- Formulaire d'ajout de matière --}}
            <div class="card-footer bg-white border-top">
                <form method="POST" action="{{ route('classes.matieres.store', $classe) }}">
                    @csrf
                    <div class="fw-semibold small mb-2">Ajouter une matière</div>
                    <div class="row g-2">
                        <div class="col-7">
                            <input type="text"
                                   name="nom"
                                   class="form-control form-control-sm @error('nom') is-invalid @enderror"
                                   placeholder="Ex: Mathématiques"
                                   value="{{ old('nom') }}"
                                   required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-3">
                            {{-- Coefficient entre 1 et 10 --}}
                            <input type="number"
                                   name="coefficient"
                                   class="form-control form-control-sm @error('coefficient') is-invalid @enderror"
                                   placeholder="Coeff"
                                   value="{{ old('coefficient', 1) }}"
                                   min="1" max="10"
                                   required>
                        </div>
                        <div class="col-2">
                            <button type="submit" class="btn btn-sm btn-primary w-100">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

    {{-- ===== COLONNE DROITE : élèves de la classe ===== --}}
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3 pb-0 d-flex justify-content-between">
                <h6 class="fw-semibold">
                    <i class="bi bi-people me-2 text-primary"></i>
                    Élèves
                    <span class="badge bg-primary bg-opacity-10 text-primary ms-1">
                        {{ $classe->eleves->count() }}
                    </span>
                </h6>
                {{-- Lien vers la saisie des notes pour cette classe --}}
                @if($classe->matieres->isNotEmpty() && $classe->eleves->isNotEmpty())
                    <a href="{{ route('notes.index', ['classe_id' => $classe->id]) }}"
                       class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil-square me-1"></i> Saisir les notes
                    </a>
                @endif
            </div>
            <div class="card-body p-0">
                @forelse($classe->eleves->sortBy('nom') as $eleve)
                <div class="d-flex justify-content-between align-items-center px-3 py-2
                            {{ !$loop->last ? 'border-bottom' : '' }}">
                    <span class="fw-semibold">{{ $eleve->nom_complet }}</span>
                    <span class="text-muted small">
                        {{ $eleve->date_naissance->format('d/m/Y') }}
                    </span>
                </div>
                @empty
                <p class="text-muted text-center py-3 mb-0 small">
                    Aucun élève dans cette classe.
                </p>
                @endforelse
            </div>
        </div>
    </div>

</div>

@endsection