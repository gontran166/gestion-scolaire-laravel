
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Gestion Scolaire</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Fond dégradé centré verticalement et horizontalement */
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1a2332 0%, #2d4a7a 100%);
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            border-radius: 16px;
            border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>

<div class="card login-card p-4">

    {{-- Logo et titre --}}
    <div class="text-center mb-4">
        <i class="bi bi-mortarboard-fill text-warning" style="font-size:3rem"></i>
        <h4 class="mt-2 fw-bold">Gestion Scolaire</h4>
        <p class="text-muted small">Cycle Primaire — Connectez-vous</p>
    </div>

    {{-- Affichage de l'erreur de validation globale (email incorrect) --}}
    @if($errors->any())
        <div class="alert alert-danger py-2">
            <i class="bi bi-exclamation-circle me-1"></i>
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Formulaire de connexion --}}
    {{-- method POST + @csrf : protection obligatoire contre les attaques CSRF --}}
    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Champ email --}}
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Adresse email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}"
                    {{-- old('email') : repopule le champ si le formulaire a été resoumis --}}
                    placeholder="admin@ecole.bf"
                    required
                    autofocus
                >
            </div>
            {{-- @error : affiche le message d'erreur Laravel pour ce champ --}}
            @error('email')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Champ mot de passe --}}
        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">Mot de passe</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="••••••••"
                    required
                >
            </div>
            @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Case "Se souvenir de moi" --}}
        <div class="mb-4 form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember">
            <label class="form-check-label text-muted" for="remember">
                Se souvenir de moi
            </label>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
            <i class="bi bi-box-arrow-in-right me-2"></i> Se connecter
        </button>
    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>