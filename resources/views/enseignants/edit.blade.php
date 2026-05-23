@extends('layouts.app')

@section('title', 'Modifier un enseignant')
@section('page-title', 'Modifier — ' . $enseignant->name)

@section('page-action')
    <a href="{{ route('enseignants.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour à la liste
    </a>
@endsection

@section('content')

<div class="row justify-content-center">
<div class="col-md-7">

<form method="POST" action="{{ route('enseignants.update', $enseignant) }}">
    @csrf
    @method('PUT')

    {{-- ===== BLOC 1 : Informations personnelles ===== --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-3 pb-0">
            <h6 class="fw-semibold">
                <i class="bi bi-person me-2 text-primary"></i>
                Informations personnelles
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">

                <div class="col-12">
                    <label for="name" class="form-label fw-semibold">
                        Nom complet <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $enseignant->name) }}"
                        required
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="email" class="form-label fw-semibold">
                        Adresse email <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $enseignant->email) }}"
                            required
                        >
                    </div>
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>
    </div>

    {{-- ===== BLOC 2 : Changement de mot de passe (optionnel) ===== --}}

    {{-- ===== BLOC 3 : Classes actuellement gérées (informatif) ===== --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-3 pb-0">
            <h6 class="fw-semibold">
                <i class="bi bi-building me-2 text-primary"></i>
                Classes actuellement gérées
            </h6>
        </div>
        <div class="card-body">
            @if($enseignant->classes->isEmpty())
                <p class="text-muted small mb-0">
                    <i class="bi bi-exclamation-circle me-1 text-warning"></i>
                    Cet enseignant n'a aucune classe assignée.
                    Vous pouvez lui en affecter une depuis la liste des enseignants.
                </p>
            @else
                <div class="d-flex flex-wrap gap-2">
                    @foreach($enseignant->classes as $classe)
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 fw-normal">
                            <i class="bi bi-building me-1"></i>
                            {{ $classe->nom }} — {{ $classe->niveau }}
                        </span>
                    @endforeach
                </div>
                <div class="form-text mt-2">
                    Pour modifier les affectations de classes, utilisez
                    <a href="{{ route('enseignants.index') }}">la liste des enseignants</a>.
                </div>
            @endif
        </div>
    </div>

    {{-- Boutons --}}
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
            <i class="bi bi-floppy me-2"></i> Enregistrer les modifications
        </button>
        <a href="{{ route('enseignants.index') }}" class="btn btn-light px-4">
            Annuler
        </a>
    </div>

</form>

{{-- ===== ZONE DE DANGER : séparée du formulaire principal ===== --}}
<div class="card border border-danger mt-4">
    <div class="card-header bg-danger bg-opacity-10 border-0 pt-3 pb-0">
        <h6 class="fw-semibold text-danger">
            <i class="bi bi-exclamation-triangle me-1"></i> Zone de danger
        </h6>
    </div>
    <div class="card-body">
        <p class="small text-muted mb-3">
            La suppression est définitive. Les classes gérées par cet enseignant
            seront libérées mais non supprimées.
        </p>
        <form method="POST"
              action="{{ route('enseignants.destroy', $enseignant) }}"
              onsubmit="return confirm('Supprimer {{ $enseignant->name }} définitivement ?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">
                <i class="bi bi-trash me-2"></i> Supprimer cet enseignant
            </button>
        </form>
    </div>
</div>

</div>
</div>

@endsection
