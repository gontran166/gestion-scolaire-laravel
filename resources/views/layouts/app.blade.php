<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- @yield('title') sera remplacé par le titre défini dans chaque vue enfant --}}
    <title>@yield('title', 'Gestion Scolaire')</title>

    {{-- Bootstrap 5 via CDN pour le style rapide et responsive --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons pour les icônes (crayon, poubelle, etc.) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* Sidebar fixe à gauche, le contenu principal s'adapte à droite */
        body { display: flex; min-height: 100vh; background-color: #f8f9fa; }

        #sidebar {
            width: 240px;
            min-height: 100vh;
            background: #1a2332; /* Bleu nuit pour l'école */
            color: #fff;
            flex-shrink: 0;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            overflow-y: auto;
            z-index: 100;
        }

        #sidebar .nav-link {
            color: #a8b2c1;
            border-radius: 6px;
            margin: 2px 8px;
            padding: 8px 12px;
            transition: background 0.2s, color 0.2s;
        }

        /* Lien actif mis en évidence */
        #sidebar .nav-link.active,
        #sidebar .nav-link:hover { background: #2d4a7a; color: #fff; }

        /* Le contenu principal laisse de la place pour la sidebar */
        #main-content { margin-left: 240px; flex: 1; padding: 24px; }

        /* Badge de rôle dans la sidebar */
        .role-badge {
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 20px;
            background: #2d4a7a;
        }

        /* Alertes flottantes en haut à droite */
        .toast-container { position: fixed; top: 20px; right: 20px; z-index: 9999; }
    </style>

    {{-- Espace pour que les vues enfants puissent ajouter du CSS supplémentaire --}}
    @stack('styles')
</head>
<body>

{{-- ===================== SIDEBAR ===================== --}}
<nav id="sidebar" class="d-flex flex-column py-3">

    {{-- En-tête de la sidebar : nom de l'école --}}
    <div class="px-3 mb-4">
        <div class="d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-mortarboard-fill fs-4 text-warning"></i>
            <span class="fw-bold fs-6">École Primaire</span>
        </div>
        <div class="text-muted" style="font-size:0.75rem">Système de gestion</div>
    </div>

    {{-- Informations de l'utilisateur connecté --}}
    <div class="px-3 mb-4 pb-3" style="border-bottom:1px solid #2d4a7a">
        <div class="fw-semibold" style="font-size:0.9rem">{{ auth()->user()->name }}</div>
        {{-- On affiche le rôle de façon lisible --}}
        <span class="role-badge text-warning mt-1 d-inline-block">
            {{ auth()->user()->role === 'gestionnaire' ? 'Gestionnaire' : 'Enseignant' }}
        </span>
    </div>

    {{-- Navigation principale --}}
    <ul class="nav flex-column flex-grow-1 px-1">

        {{-- Lien dashboard : visible par tous --}}
        <li class="nav-item">
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i> Tableau de bord
            </a>
        </li>

        {{-- Section réservée au gestionnaire --}}
        @if(auth()->user()->isGestionnaire())
            <li class="mt-3 px-3" style="font-size:0.7rem;color:#5a7a9a;text-transform:uppercase;letter-spacing:1px">
                Administration
            </li>

            <li class="nav-item">
                <a href="{{ route('classes.index') }}"
                   class="nav-link {{ request()->routeIs('classes.*') ? 'active' : '' }}">
                    <i class="bi bi-building me-2"></i> Classes
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('enseignants.index') }}"
                    class="nav-link {{ request()->routeIs('enseignants.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge me-2"></i> Enseignants
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('eleves.index') }}"
                   class="nav-link {{ request()->routeIs('eleves.*') ? 'active' : '' }}">
                    <i class="bi bi-people me-2"></i> Élèves
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('paiements.create') }}"
                   class="nav-link {{ request()->routeIs('paiements.*') ? 'active' : '' }}">
                    <i class="bi bi-cash-coin me-2"></i> Paiements
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('dashboard.impayes') }}"
                   class="nav-link {{ request()->routeIs('dashboard.impayes') ? 'active' : '' }}">
                    <i class="bi bi-exclamation-triangle me-2"></i> Impayés
                </a>
            </li>
        @endif

        {{-- Section pédagogique : visible par tous --}}
        <li class="mt-3 px-3" style="font-size:0.7rem;color:#5a7a9a;text-transform:uppercase;letter-spacing:1px">
            Pédagogie
        </li>

        <li class="nav-item">
            <a href="{{ route('notes.index') }}"
               class="nav-link {{ request()->routeIs('notes.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text me-2"></i> Notes
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="{{ route('profil.password.edit') }}"
                class="nav-link {{ request()->routeIs('profil.*') ? 'active' : '' }}">
                <i class="bi bi-key me-2"></i> Changer mon mot de passe
            </a>
        </li>

    </ul>

    {{-- Bouton de déconnexion en bas de la sidebar --}}
    <div class="px-3 mt-auto pt-3" style="border-top:1px solid #2d4a7a">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-sm w-100 text-start nav-link">
                <i class="bi bi-box-arrow-left me-2"></i> Déconnexion
            </button>
        </form>
    </div>
</nav>

{{-- ===================== CONTENU PRINCIPAL ===================== --}}
<main id="main-content">

    {{-- En-tête de page : titre injecté par chaque vue enfant --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0 fw-semibold">@yield('page-title')</h1>
        {{-- Zone optionnelle pour un bouton d'action (ex: "Ajouter un élève") --}}
        @yield('page-action')
    </div>

    {{-- Zone principale : chaque vue enfant injecte son HTML ici --}}
    @yield('content')
</main>

{{-- ===================== NOTIFICATIONS FLASH ===================== --}}
{{-- Les messages de succès/erreur envoyés via ->with('success', '...') s'affichent ici --}}
<div class="toast-container">

    @if(session('success'))
    {{-- Toast vert pour les succès --}}
    <div class="toast show align-items-center text-bg-success border-0 mb-2" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif

    @if(session('error'))
    {{-- Toast rouge pour les erreurs --}}
    <div class="toast show align-items-center text-bg-danger border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-x-circle me-2"></i>{{ session('error') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif

</div>

{{-- Bootstrap JS (nécessaire pour les toasts, modals, etc.) --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

{{-- Auto-fermeture des toasts après 4 secondes --}}
<script>
    document.querySelectorAll('.toast').forEach(function(el) {
        setTimeout(function() {
            var toast = bootstrap.Toast.getOrCreateInstance(el);
            toast.hide();
        }, 4000);
    });
</script>

{{-- Espace pour les scripts supplémentaires des vues enfants --}}
@stack('scripts')

</body>
</html>