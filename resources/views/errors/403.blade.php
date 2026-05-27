@extends('layouts.app')

@section('title', 'Accès refusé')
@section('page-title', 'Accès non autorisé')

@section('content')

<div class="card border-0 shadow-sm text-center py-5">
    <div>
        {{-- Icône de cadenas --}}
        <i class="bi bi-lock-fill text-danger" style="font-size:4rem"></i>

        <h3 class="mt-3 fw-bold">Accès refusé</h3>

        <p class="text-muted mt-2 mb-4">
            Vous n'avez pas les droits nécessaires pour accéder à cette page.<br>
            Cette section est réservée aux directeur de l'école.
        </p>

        {{-- Lien de retour vers le dashboard --}}
        <a href="{{ route('dashboard') }}" class="btn btn-primary px-4">
            <i class="bi bi-house me-2"></i> Retour au tableau de bord
        </a>
    </div>
</div>

@endsection