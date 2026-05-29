<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>École Primaire Boussougou Communale</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* ── Variables de couleur ── */
        :root {
            --bleu-ecole  : #1a2332;
            --bleu-clair  : #2d4a7a;
            --or-ecole    : #f5a623;
            --vert-ecole  : #2e7d52;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            background: var(--bleu-ecole);
            color: #fff;
            overflow-x: hidden;
        }

        /* ── Fond animé avec des cercles décoratifs ── */
        .bg-decoration {
            position: fixed;
            inset: 0;
            overflow: hidden;
            z-index: 0;
            pointer-events: none;
        }
        .bg-decoration .cercle {
            position: absolute;
            border-radius: 50%;
            opacity: 0.06;
            background: var(--or-ecole);
            animation: flottement 8s ease-in-out infinite;
        }
        .bg-decoration .cercle:nth-child(1) { width:400px; height:400px; top:-100px; right:-100px; animation-delay:0s; }
        .bg-decoration .cercle:nth-child(2) { width:300px; height:300px; bottom:50px; left:-80px;  animation-delay:2s; }
        .bg-decoration .cercle:nth-child(3) { width:200px; height:200px; top:40%;    right:10%;    animation-delay:4s; }

        @keyframes flottement {
            0%, 100% { transform: translateY(0px) scale(1); }
            50%       { transform: translateY(-20px) scale(1.05); }
        }

        /* ── Structure principale ── */
        .page-wrapper {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Barre de navigation supérieure ── */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 40px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            backdrop-filter: blur(4px);
        }
        .top-bar .annee-scolaire {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 20px;
            padding: 4px 16px;
            font-size: 0.8rem;
            color: rgba(255,255,255,0.8);
        }
        .top-bar .heure {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.6);
        }

        /* ── Section héro : logo + nom de l'école ── */
        .hero {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 60px 20px 40px;
        }

        /* Emblème circulaire de l'école */
        .school-emblem {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--or-ecole), #e08c10);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 28px;
            box-shadow: 0 8px 32px rgba(245,166,35,0.35);
            /* Animation légère de pulsation */
            animation: pulse 3s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 8px 32px rgba(245,166,35,0.35); }
            50%       { box-shadow: 0 8px 48px rgba(245,166,35,0.55); }
        }
        .school-emblem i { font-size: 3rem; color: var(--bleu-ecole); }

        .school-name {
            font-size: clamp(1.6rem, 4vw, 2.6rem);
            /* clamp : taille fluide entre 1.6rem (mobile) et 2.6rem (desktop) */
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }
        .school-name .accent { color: var(--or-ecole); }

        .school-meta {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.55);
            margin-bottom: 12px;
        }

        /* Ligne de séparation dorée */
        .divider-gold {
            width: 60px;
            height: 3px;
            background: var(--or-ecole);
            border-radius: 2px;
            margin: 20px auto 28px;
        }

        /* Petits badges d'infos : élèves, enseignants, etc. */
        .school-stats {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 50px;
        }
        .stat-pill {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 30px;
            padding: 6px 18px;
            font-size: 0.8rem;
            color: rgba(255,255,255,0.75);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .stat-pill i { color: var(--or-ecole); }

        /* ── Cartes de connexion ── */
        .login-cards {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 50px;
        }

        .login-card {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 20px;
            padding: 36px 32px;
            width: 220px;
            text-align: center;
            text-decoration: none;
            color: #fff;
            cursor: pointer;
            transition: all 0.3s ease;
            /* Passage via le formulaire de login avec rôle présélectionné --}}
            position: relative;
            overflow: hidden;
        }

        /* Reflet lumineux au survol */
        .login-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 60%);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .login-card:hover::before { opacity: 1; }

        .login-card:hover {
            transform: translateY(-6px);
            border-color: rgba(255,255,255,0.3);
            color: #fff;
            text-decoration: none;
        }

        /* Carte directeur : accent doré */
        .login-card.directeur:hover {
            background: rgba(245,166,35,0.15);
            border-color: rgba(245,166,35,0.5);
            box-shadow: 0 12px 40px rgba(245,166,35,0.2);
        }

        /* Carte enseignant : accent vert */
        .login-card.enseignant:hover {
            background: rgba(46,125,82,0.2);
            border-color: rgba(46,125,82,0.5);
            box-shadow: 0 12px 40px rgba(46,125,82,0.2);
        }

        .login-card .card-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            font-size: 1.8rem;
            transition: transform 0.3s;
        }
        .login-card:hover .card-icon { transform: scale(1.1); }

        .login-card.directeur .card-icon {
            background: rgba(245,166,35,0.2);
            color: var(--or-ecole);
        }
        .login-card.enseignant .card-icon {
            background: rgba(46,125,82,0.25);
            color: #5bc98a;
        }

        .login-card .card-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 6px;
        }
        .login-card .card-desc {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.5);
            line-height: 1.4;
        }
        .login-card .card-arrow {
            margin-top: 18px;
            font-size: 0.75rem;
            color: rgba(255,255,255,0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            transition: gap 0.2s;
        }
        .login-card:hover .card-arrow { gap: 8px; }

        /* ── Pied de page ── */
        .page-footer {
            text-align: center;
            padding: 20px 40px;
            border-top: 1px solid rgba(255,255,255,0.07);
            font-size: 0.75rem;
            color: rgba(255,255,255,0.3);
        }

        /* ── Responsive mobile ── */
        @media (max-width: 576px) {
            .top-bar { padding: 12px 20px; }
            .hero { padding: 40px 16px 30px; }
            .login-card { width: 160px; padding: 28px 20px; }
        }
    </style>
</head>
<body>

{{-- Cercles décoratifs en arrière-plan --}}
<div class="bg-decoration">
    <div class="cercle"></div>
    <div class="cercle"></div>
    <div class="cercle"></div>
</div>

<div class="page-wrapper">

    {{-- ── BARRE SUPÉRIEURE ── --}}
    <div class="top-bar">
        <span class="annee-scolaire">
            <i class="bi bi-calendar3 me-1"></i>
            Année scolaire 2025 – 2026
        </span>
        {{-- Heure en temps réel mise à jour par JS --}}
        <span class="heure" id="horloge"></span>
    </div>

    {{-- ── SECTION HÉRO ── --}}
    <div class="hero">

        {{-- Emblème --}}
        <div class="school-emblem">
            <i class="bi bi-mortarboard-fill"></i>
        </div>

        {{-- Nom de l'école --}}
        <h1 class="school-name">
            École Primaire<br>
            <span class="accent">Boussougou</span> Communale
        </h1>

        {{-- Date de création --}}
        <p class="school-meta">
            <i class="bi bi-geo-alt me-1"></i>
            Fondée le 23 avril 2000 · Burkina Faso
        </p>

        <div class="divider-gold"></div>

        {{-- Badges d'informations --}}
        <div class="school-stats">
            <div class="stat-pill">
                <i class="bi bi-people-fill"></i>
                Cycle CP1 → CM2
            </div>
            <div class="stat-pill">
                <i class="bi bi-shield-check-fill"></i>
                Gestion numérique
            </div>
            <div class="stat-pill">
                <i class="bi bi-award-fill"></i>
                Éducation de qualité
            </div>
        </div>

        {{-- ── CARTES DE CONNEXION ── --}}
        <div class="login-cards">

            {{-- Carte Directeur --}}
            <a href="{{ route('connexion') }}?role=gestionnaire" class="login-card directeur">
                <div class="card-icon">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div class="card-title">Directeur</div>
                <div class="card-desc">
                    Gestion administrative,<br>financière et globale
                </div>
                <div class="card-arrow">
                    Se connecter <i class="bi bi-arrow-right"></i>
                </div>
            </a>

            {{-- Carte Enseignant --}}
            <a href="{{ route('connexion') }}?role=enseignant" class="login-card enseignant">
                <div class="card-icon">
                    <i class="bi bi-person-workspace"></i>
                </div>
                <div class="card-title">Enseignant</div>
                <div class="card-desc">
                    Saisie des notes<br>et suivi pédagogique
                </div>
                <div class="card-arrow">
                    Se connecter <i class="bi bi-arrow-right"></i>
                </div>
            </a>

        </div>

        {{-- Message de sécurité --}}
        <p style="font-size:0.75rem; color:rgba(255,255,255,0.3)">
            <i class="bi bi-lock-fill me-1"></i>
            Accès réservé au personnel autorisé de l'établissement
        </p>

    </div>

    {{-- ── PIED DE PAGE ── --}}
    <div class="page-footer">
        © {{ date('Y') }} École Primaire Boussougou Communale · Système de Gestion Scolaire
    </div>

</div>

<script>
    /**
     * Horloge en temps réel affichée dans la barre supérieure
     * Met à jour toutes les secondes
     */
    function mettreAJourHorloge() {
        const maintenant = new Date();
        const options    = {
            weekday : 'long',
            day     : 'numeric',
            month   : 'long',
            year    : 'numeric',
            hour    : '2-digit',
            minute  : '2-digit',
            second  : '2-digit',
        };
        // toLocaleDateString avec la locale française
        document.getElementById('horloge').textContent =
            maintenant.toLocaleDateString('fr-FR', options);
    }

    // Appel immédiat puis toutes les secondes
    mettreAJourHorloge();
    setInterval(mettreAJourHorloge, 1000);
</script>

</body>
</html>