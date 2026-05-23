@extends('layouts.app')

@section('title', 'Mon espace')
@section('page-title', 'Mon espace enseignant')

@section('content')

<div class="row g-3 mb-4">

    {{-- Carte : nombre de classes de l'enseignant --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-primary bg-opacity-10">
                    <i class="bi bi-building text-primary fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Mes classes</div>
                    <div class="fw-bold fs-4">{{ $classes->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Carte : nombre total d'élèves dans ses classes --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-success bg-opacity-10">
                    <i class="bi bi-people text-success fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Mes élèves</div>
                    {{-- sum() sur la collection pour totaliser les élèves de toutes ses classes --}}
                    <div class="fw-bold fs-4">
                        {{ $classes->sum(fn($c) => $c->eleves->count()) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Carte : accès rapide à la saisie de notes --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-warning bg-opacity-10">
                    <i class="bi bi-journal-text text-warning fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Saisie des notes</div>
                    <a href="{{ route('notes.index') }}" class="btn btn-sm btn-warning mt-1">
                        Accéder <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Liste de ses classes avec leurs élèves --}}
@forelse($classes as $classe)
<div class="card border-0 shadow-sm mb-3">

    {{-- En-tête avec nom de la classe et liens rapides --}}
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div>
            <span class="badge bg-primary bg-opacity-10 text-primary me-2">
                {{ $classe->niveau }}
            </span>
            <span class="fw-semibold">{{ $classe->nom }}</span>
            <span class="text-muted small ms-2">
                {{ $classe->eleves->count() }} élève(s)
            </span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('notes.index', ['classe_id' => $classe->id]) }}"
               class="btn btn-sm btn-outline-primary">
                <i class="bi bi-pencil-square me-1"></i> Saisir les notes
            </a>
            <a href="{{ route('notes.classement', $classe) }}"
               class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-trophy me-1"></i> Classement
            </a>
        </div>
    </div>

    {{-- Liste compacte des élèves de cette classe --}}
    <div class="card-body p-0">
        @if($classe->eleves->isEmpty())
            <p class="text-muted text-center py-3 mb-0 small">
                Aucun élève dans cette classe.
            </p>
        @else
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Élève</th>
                            <th class="text-center">Date de naissance</th>
                            <th class="text-center">Âge</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classe->eleves->sortBy('nom') as $eleve)
                        <tr>
                            <td class="fw-semibold">{{ $eleve->nom_complet }}</td>
                            <td class="text-center text-muted">
                                {{ $eleve->date_naissance->format('d/m/Y') }}
                            </td>
                            <td class="text-center">
                                {{-- Calcul dynamique de l'âge à partir de la date de naissance --}}
                                {{ $eleve->date_naissance->age }} ans
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

@empty
{{-- L'enseignant n'a encore aucune classe assignée --}}
<div class="card border-0 shadow-sm text-center py-5">
    <div class="text-muted">
        <i class="bi bi-building fs-1 d-block mb-2 opacity-25"></i>
        <p>Aucune classe ne vous est encore assignée.</p>
        <p class="small">Contactez le gestionnaire de l'établissement.</p>
    </div>
</div>
@endforelse

@endsection