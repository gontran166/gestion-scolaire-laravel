{{-- resources/views/enseignants/create.blade.php --}}
{{-- Scénario : le directeur reçoit un nouvel enseignant et crée son compte --}}

@extends('layouts.app')

@section('title', 'Ajouter un enseignant')
@section('page-title', 'Ajouter un nouvel enseignant')

@section('page-action')
    <a href="{{ route('enseignants.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour à la liste
    </a>
@endsection

@section('content')

<div class="row justify-content-center">
<div class="col-md-7">

<form method="POST" action="{{ route('enseignants.store') }}">
    @csrf

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

                {{-- Nom complet --}}
                <div class="col-12">
                    <label for="name" class="form-label fw-semibold">
                        Nom complet <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
                        placeholder="Ex: OUÉDRAOGO Jean"
                        required
                        autofocus
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email (sera utilisé pour la connexion) --}}
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
                            value="{{ old('email') }}"
                            placeholder="jean.ouedraogo@ecole.bf"
                            required
                        >
                    </div>
                    {{-- Info : cet email sera l'identifiant de connexion --}}
                    <div class="form-text">
                        <i class="bi bi-info-circle me-1"></i>
                        Cet email sera utilisé pour se connecter à l'application.
                    </div>
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>
    </div>

    {{-- ===== BLOC 2 : Mot de passe ===== --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-3 pb-0">
            <h6 class="fw-semibold">
                <i class="bi bi-lock me-2 text-primary"></i>
                Accès à l'application
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-6">
                    <label for="password" class="form-label fw-semibold">
                        Mot de passe <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Min. 8 caractères"
                            required
                        >
                        {{-- Bouton œil pour afficher/masquer le mot de passe --}}
                        <button type="button"
                                class="btn btn-outline-secondary"
                                onclick="togglePassword('password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label fw-semibold">
                        Confirmer le mot de passe <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control"
                            placeholder="Répéter le mot de passe"
                            required
                        >
                        <button type="button"
                                class="btn btn-outline-secondary"
                                onclick="togglePassword('password_confirmation', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

            </div>

            {{-- Alerte d'information sur les bonnes pratiques --}}
            <div class="alert alert-info py-2 mt-3 mb-0 small">
                <i class="bi bi-shield-check me-1"></i>
                Communiquez ces identifiants à l'enseignant en mains propres.
                Il pourra changer son mot de passe après connexion.
            </div>

        </div>
    </div>

    {{-- ===== BLOC 3 : Affectation de classe (optionnelle) ===== --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-3 pb-0">
            <h6 class="fw-semibold">
                <i class="bi bi-building me-2 text-primary"></i>
                Affectation de classe
                <small class="text-muted fw-normal">(optionnel)</small>
            </h6>
        </div>
        <div class="card-body">

            <label for="classe_id" class="form-label">
                Classe à confier à cet enseignant
            </label>
            <select
                id="classe_id"
                name="classe_id"
                class="form-select @error('classe_id') is-invalid @enderror"
            >
                <option value="">— Affecter une classe plus tard —</option>

                {{-- On regroupe les classes par statut : libres d'abord, déjà prises ensuite --}}
                @php
                    $classesLibres = $classes->whereNull('user_id');
                    $classesPrises = $classes->whereNotNull('user_id');
                @endphp

                @if($classesLibres->isNotEmpty())
                    <optgroup label="✓ Classes disponibles">
                        @foreach($classesLibres as $classe)
                            <option value="{{ $classe->id }}"
                                {{ old('classe_id') == $classe->id ? 'selected' : '' }}>
                                {{ $classe->nom }} — {{ $classe->niveau }}
                                ({{ number_format($classe->frais_scolarite, 0, ',', ' ') }} F/an)
                            </option>
                        @endforeach
                    </optgroup>
                @endif

                @if($classesPrises->isNotEmpty())
                    {{-- Les classes déjà prises sont visibles mais signalées --}}
                    <optgroup label="⚠ Classes déjà assignées (réaffectation possible)">
                        @foreach($classesPrises as $classe)
                            <option value="{{ $classe->id }}"
                                {{ old('classe_id') == $classe->id ? 'selected' : '' }}>
                                {{ $classe->nom }} — actuellement : {{ $classe->enseignant->name }}
                            </option>
                        @endforeach
                    </optgroup>
                @endif

            </select>

            <div class="form-text">
                <i class="bi bi-info-circle me-1"></i>
                Vous pourrez aussi affecter ou changer la classe depuis la liste des enseignants.
            </div>

            @error('classe_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror

        </div>
    </div>

    {{-- Boutons de validation --}}
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
            <i class="bi bi-person-check me-2"></i> Créer le compte enseignant
        </button>
        <a href="{{ route('enseignants.index') }}" class="btn btn-light px-4">
            Annuler
        </a>
    </div>

</form>

</div>
</div>

@endsection

@push('scripts')
<script>
    /**
     * Bascule la visibilité d'un champ mot de passe.
     * @param {string} fieldId - l'id du champ input
     * @param {HTMLButtonElement} btn - le bouton œil cliqué
     */
    function togglePassword(fieldId, btn) {
        const input = document.getElementById(fieldId);
        const icon  = btn.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            // Œil barré : indique que le mot de passe est visible
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }
</script>
@endpush