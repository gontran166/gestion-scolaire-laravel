@extends('layouts.app')

@section('title', 'Enseignants')
@section('page-title', 'Gestion des enseignants')

@section('page-action')
    <a href="{{ route('enseignants.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Ajouter un enseignant
    </a>
@endsection

@section('content')

<div class="row g-4">

    {{-- ===== COLONNE GAUCHE : liste des enseignants ===== --}}
    <div class="col-md-8">

        @forelse($enseignants as $enseignant)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-start">

                    {{-- Avatar avec initiales + infos de base --}}
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary bg-opacity-10
                                    d-flex align-items-center justify-content-center
                                    text-primary fw-bold flex-shrink-0"
                             style="width:48px; height:48px; font-size:1.1rem">
                            {{-- Initiales : première lettre du prénom et du nom --}}
                            {{ strtoupper(substr($enseignant->name, 0, 2)) }}
                        </div>

                        <div>
                            <div class="fw-bold">{{ $enseignant->name }}</div>
                            <div class="text-muted small">
                                <i class="bi bi-envelope me-1"></i>{{ $enseignant->email }}
                            </div>
                            {{-- Nombre de classes gérées --}}
                            <div class="mt-1">
                                @if($enseignant->classes_count === 0)
                                    <span class="badge bg-warning bg-opacity-15 text-warning">
                                        <i class="bi bi-exclamation-circle me-1"></i>
                                        Aucune classe assignée
                                    </span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success">
                                        <i class="bi bi-building me-1"></i>
                                        {{ $enseignant->classes_count }}
                                        classe(s) assignée(s)
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Menu d'actions --}}
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li>
                                <a class="dropdown-item"
                                   href="{{ route('enseignants.edit', $enseignant) }}">
                                    <i class="bi bi-pencil me-2 text-primary"></i> Modifier
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST"
                                      action="{{ route('enseignants.destroy', $enseignant) }}"
                                      onsubmit="return confirm('Supprimer {{ $enseignant->name }} ? Ses classes seront libérées.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-trash me-2"></i> Supprimer
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>

                </div>

                {{-- Classes actuellement affectées à cet enseignant --}}
                @if($enseignant->classes->isNotEmpty())
                    <div class="mt-3 pt-3" style="border-top:1px dashed #dee2e6">
                        <div class="small text-muted mb-2">
                            <i class="bi bi-building me-1"></i> Classes gérées :
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($enseignant->classes as $classe)
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge bg-primary bg-opacity-10 text-primary fw-normal px-2 py-1">
                                    {{ $classe->nom }}
                                    <span class="text-muted">({{ $classe->niveau }})</span>
                                </span>
                                {{-- Bouton pour retirer la classe de cet enseignant --}}
                                <form method="POST"
                                      action="{{ route('classes.update', $classe) }}"
                                      onsubmit="return confirm('Désaffecter la classe {{ $classe->nom }} ?')">
                                    @csrf
                                    @method('PUT')
                                    {{-- On envoie uniquement user_id null pour retirer l'enseignant --}}
                                    <input type="hidden" name="nom" value="{{ $classe->nom }}">
                                    <input type="hidden" name="niveau" value="{{ $classe->niveau }}">
                                    <input type="hidden" name="frais_scolarite" value="{{ $classe->frais_scolarite }}">
                                    <input type="hidden" name="annee_scolaire" value="{{ $classe->annee_scolaire }}">
                                    <input type="hidden" name="user_id" value="">
                                    <button type="submit"
                                            class="btn btn-sm p-0 text-danger"
                                            title="Retirer cette classe"
                                            style="line-height:1">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </form>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Widget d'affectation rapide : sélecteur de classe + bouton --}}
                <div class="mt-3">
                    <form method="POST"
                          action="{{ route('enseignants.affecter-classe', $enseignant) }}"
                          class="d-flex gap-2 align-items-center">
                        @csrf
                        <select name="classe_id"
                                class="form-select form-select-sm"
                                style="max-width:220px"
                                required>
                            <option value="">+ Affecter une classe...</option>
                            {{-- On n'affiche que les classes non encore affectées à cet enseignant --}}
                            @foreach($classesLibres->reject(fn($c) => $enseignant->classes->contains($c)) as $libre)
                                <option value="{{ $libre->id }}">
                                    {{ $libre->nom }} ({{ $libre->niveau }})
                                </option>
                            @endforeach
                            {{-- Séparateur visuel si des classes sont déjà prises --}}
                            @if($classesLibres->reject(fn($c) => $enseignant->classes->contains($c))->isEmpty())
                                <option disabled>Aucune classe disponible</option>
                            @endif
                        </select>
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-check-lg me-1"></i> Affecter
                        </button>
                    </form>
                </div>

            </div>
        </div>

        @empty
        {{-- Aucun enseignant dans le système --}}
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="text-muted">
                <i class="bi bi-people fs-1 d-block mb-3 opacity-25"></i>
                <p class="mb-3">Aucun enseignant enregistré.</p>
                <a href="{{ route('enseignants.create') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus me-1"></i> Ajouter le premier enseignant
                </a>
            </div>
        </div>
        @endforelse

    </div>

    {{-- ===== COLONNE DROITE : résumé et classes sans enseignant ===== --}}
    <div class="col-md-4">

        {{-- Résumé rapide --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-semibold">Résumé</h6>
            </div>
            <div class="card-body">

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total enseignants</span>
                    <span class="fw-bold">{{ $enseignants->count() }}</span>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Avec classe assignée</span>
                    {{-- Filtre : enseignants qui ont au moins une classe --}}
                    <span class="fw-bold text-success">
                        {{ $enseignants->filter(fn($e) => $e->classes_count > 0)->count() }}
                    </span>
                </div>

                <div class="d-flex justify-content-between">
                    <span class="text-muted">Sans classe</span>
                    <span class="fw-bold text-warning">
                        {{ $enseignants->filter(fn($e) => $e->classes_count === 0)->count() }}
                    </span>
                </div>

            </div>
        </div>

        {{-- Classes sans enseignant --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-semibold">
                    Classes sans responsable
                    @if($classesLibres->isNotEmpty())
                        <span class="badge bg-warning text-dark ms-1">
                            {{ $classesLibres->count() }}
                        </span>
                    @endif
                </h6>
            </div>
            <div class="card-body">
                @forelse($classesLibres as $libre)
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2"
                         style="{{ !$loop->last ? 'border-bottom:1px dashed #dee2e6' : '' }}">
                        <div>
                            <span class="fw-semibold small">{{ $libre->nom }}</span>
                            <span class="text-muted small ms-1">({{ $libre->niveau }})</span>
                        </div>
                        {{-- Lien direct vers la liste pour affecter depuis là --}}
                        <span class="badge bg-warning bg-opacity-15 text-warning small">
                            Non assignée
                        </span>
                    </div>
                @empty
                    <p class="text-muted small text-center mb-0">
                        <i class="bi bi-check-circle text-success me-1"></i>
                        Toutes les classes ont un responsable.
                    </p>
                @endforelse
            </div>
        </div>

    </div>

</div>

@endsection