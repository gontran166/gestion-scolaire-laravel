
@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('content')

{{-- ===== LIGNE 1 : CARTES STATISTIQUES ===== --}}
{{-- 4 cartes côte à côte résumant l'état global de l'école --}}
<div class="row g-3 mb-4">

    {{-- Carte : nombre total d'élèves inscrits --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-primary bg-opacity-10">
                    <i class="bi bi-people-fill text-primary fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Total élèves</div>
                    <div class="fw-bold fs-4">{{ $stats['total_eleves'] }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Carte : frais collectés --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-success bg-opacity-10">
                    <i class="bi bi-cash-stack text-success fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Frais collectés</div>
                    {{-- number_format : affiche le nombre avec séparateur de milliers --}}
                    <div class="fw-bold fs-4">{{ number_format($stats['frais_collectes'], 0, ',', ' ') }} F</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Carte : taux de recouvrement avec barre de progression --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="rounded-3 p-3 bg-warning bg-opacity-10">
                        <i class="bi bi-pie-chart-fill text-warning fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Taux recouvrement</div>
                        <div class="fw-bold fs-4">{{ $stats['taux_recouvrement'] }}%</div>
                    </div>
                </div>
                {{-- Barre de progression colorée selon le taux --}}
                <div class="progress" style="height:6px">
                    <div class="progress-bar
                        {{-- Couleur conditionnelle : vert > 75%, orange > 50%, rouge sinon --}}
                        {{ $stats['taux_recouvrement'] >= 75 ? 'bg-success' : ($stats['taux_recouvrement'] >= 50 ? 'bg-warning' : 'bg-danger') }}"
                        style="width: {{ min(100, $stats['taux_recouvrement']) }}%">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Carte : élèves en impayé (avec lien vers la liste détaillée) --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 {{ $stats['nb_impayes'] > 0 ? 'border-danger border' : '' }}">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-danger bg-opacity-10">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Élèves en impayé</div>
                    <div class="fw-bold fs-4 text-danger">{{ $stats['nb_impayes'] }}</div>
                    @if($stats['nb_impayes'] > 0)
                        <a href="{{ route('dashboard.impayes') }}" class="small text-danger">
                            Voir la liste →
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ===== LIGNE 2 : RÉCAPITULATIF FINANCIER ===== --}}
<div class="row g-3">

    {{-- Bloc : comparaison frais attendus vs frais collectés --}}
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-semibold mb-0">Situation financière globale</h6>
            </div>
            <div class="card-body">

                {{-- Frais attendus --}}
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Frais attendus (total)</span>
                    <span class="fw-semibold">
                        {{ number_format($stats['frais_attendus'], 0, ',', ' ') }} F CFA
                    </span>
                </div>

                {{-- Frais collectés --}}
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Frais collectés</span>
                    <span class="fw-semibold text-success">
                        {{ number_format($stats['frais_collectes'], 0, ',', ' ') }} F CFA
                    </span>
                </div>

                <hr class="my-2">

                {{-- Reste à encaisser = attendus - collectés --}}
                @php
                    $reste = $stats['frais_attendus'] - $stats['frais_collectes'];
                @endphp
                <div class="d-flex justify-content-between">
                    <span class="fw-semibold">Reste à encaisser</span>
                    <span class="fw-bold text-danger">
                        {{ number_format(max(0, $reste), 0, ',', ' ') }} F CFA
                    </span>
                </div>

                {{-- Grande barre de progression visuelle --}}
                <div class="progress mt-3" style="height:12px; border-radius:8px">
                    <div class="progress-bar bg-success"
                         style="width: {{ min(100, $stats['taux_recouvrement']) }}%"
                         title="{{ $stats['taux_recouvrement'] }}% encaissé">
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-1">
                    <small class="text-muted">0%</small>
                    <small class="text-success fw-semibold">{{ $stats['taux_recouvrement'] }}% encaissé</small>
                    <small class="text-muted">100%</small>
                </div>

            </div>
        </div>
    </div>

    {{-- Bloc : accès rapides --}}
    <div class="col-md-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-semibold mb-0">Accès rapides</h6>
            </div>
            <div class="card-body d-flex flex-column gap-2">
                <a href="{{ route('eleves.create') }}" class="btn btn-outline-primary text-start">
                    <i class="bi bi-person-plus me-2"></i> Inscrire un élève
                </a>
                <a href="{{ route('paiements.create') }}" class="btn btn-outline-success text-start">
                    <i class="bi bi-cash-coin me-2"></i> Enregistrer un paiement
                </a>
                <a href="{{ route('classes.create') }}" class="btn btn-outline-secondary text-start">
                    <i class="bi bi-plus-circle me-2"></i> Créer une classe
                </a>
                <a href="{{ route('dashboard.impayes') }}" class="btn btn-outline-danger text-start">
                    <i class="bi bi-exclamation-triangle me-2"></i> Voir les impayés
                </a>
                <a href="{{ route('enseignants.index') }}" class="btn btn-outline-primary text-start">
                    <i class="bi bi-person-badge me-2"></i> Gérer les enseignants
                </a>
            </div>
        </div>
    </div>

</div>

@endsection