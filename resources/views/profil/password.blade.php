@extends('layouts.app')

@section('title', 'Changer mon mot de passe')
@section('page-title', 'Changer mon mot de passe')

@section('content')

<div class="row justify-content-center">
<div class="col-md-6">

    {{-- Carte d'identité de l'utilisateur connecté (lecture seule) --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body d-flex align-items-center gap-3">
            <div class="rounded-circle bg-primary bg-opacity-10
                        d-flex align-items-center justify-content-center
                        text-primary fw-bold flex-shrink-0"
                 style="width:52px; height:52px; font-size:1.2rem">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div>
                <div class="fw-bold">{{ auth()->user()->name }}</div>
                <div class="text-muted small">{{ auth()->user()->email }}</div>
                <span class="badge bg-secondary bg-opacity-10 text-secondary mt-1">
                    {{ auth()->user()->role === 'gestionnaire' ? 'Gestionnaire' : 'Enseignant' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Formulaire de changement de mot de passe --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-3 pb-0">
            <h6 class="fw-semibold">
                <i class="bi bi-lock me-2 text-primary"></i>
                Nouveau mot de passe
            </h6>
        </div>
        <div class="card-body">

            <form method="POST" action="{{ route('profil.password.update') }}">
                @csrf
                @method('PUT')

                {{-- Mot de passe actuel --}}
                <div class="mb-3">
                    <label for="current_password" class="form-label fw-semibold">
                        Mot de passe actuel <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            class="form-control @error('current_password') is-invalid @enderror"
                            placeholder="Votre mot de passe actuel"
                            required
                            autofocus
                        >
                        <button type="button"
                                class="btn btn-outline-secondary"
                                onclick="togglePassword('current_password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    {{-- Le message d'erreur vient du controller si le mdp actuel est faux --}}
                    @error('current_password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-3">

                {{-- Nouveau mot de passe --}}
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">
                        Nouveau mot de passe <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Minimum 8 caractères"
                            required
                        >
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

                {{-- Confirmation du nouveau mot de passe --}}
                <div class="mb-4">
                    <label for="password_confirmation" class="form-label fw-semibold">
                        Confirmer le nouveau mot de passe <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control"
                            placeholder="Répéter le nouveau mot de passe"
                            required
                        >
                        <button type="button"
                                class="btn btn-outline-secondary"
                                onclick="togglePassword('password_confirmation', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                {{-- Avertissement : la modification déconnecte --}}
                <div class="alert alert-warning py-2 small mb-4">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Après modification, vous serez <strong>déconnecté automatiquement</strong>
                    et devrez vous reconnecter avec le nouveau mot de passe.
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                    <i class="bi bi-shield-check me-2"></i> Modifier mon mot de passe
                </button>

            </form>

        </div>
    </div>

    <div class="text-center mt-3">
        <a href="{{ route('dashboard') }}" class="text-muted small">
            <i class="bi bi-arrow-left me-1"></i> Retour au tableau de bord
        </a>
    </div>

</div>
</div>

@endsection

@push('scripts')
<script>
    function togglePassword(fieldId, btn) {
        const input = document.getElementById(fieldId);
        const icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }
</script>
@endpush