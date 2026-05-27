
@extends('layouts.app')

@section('title', 'Classes')
@section('page-title', 'Gestion des classes')

@section('page-action')
    <a href="{{ route('classes.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Créer une classe
    </a>
@endsection

@section('content')

<div class="row g-3">

    @forelse($classes as $classe)
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">

            {{-- niveau et nom --}}
            <div class="card-header border-0 pt-3 d-flex justify-content-between align-items-start">
                <div>
                    {{-- Badge de niveau coloré selon le cycle --}}
                    @php
                        $couleurNiveau = match($classe->niveau) {
                            'CP1', 'CP2'        => 'info',
                            'CE1', 'CE2'        => 'primary',
                            'CM1', 'CM2'        => 'success',
                            default             => 'secondary',
                        };
                    @endphp
                    <span class="badge bg-{{ $couleurNiveau }} mb-1">
                        {{ $classe->niveau }}
                    </span>
                    <h6 class="fw-bold mb-0">{{ $classe->nom }}</h6>
                </div>
                {{-- Menu d'actions (modifier / supprimer) --}}
                <div class="dropdown">
                    <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('classes.edit', $classe) }}">
                                <i class="bi bi-pencil me-2"></i> Modifier
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST"
                                  action="{{ route('classes.destroy', $classe) }}"
                                  onsubmit="return confirm('Supprimer la classe {{ $classe->nom }} ?')">
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

            <div class="card-body pt-2">

                {{-- Enseignant responsable --}}
                <div class="d-flex align-items-center gap-2 mb-3 text-muted small">
                    <i class="bi bi-person-badge"></i>
                    {{ $classe->enseignant?->name ?? 'Aucun enseignant assigné' }}
                </div>

                {{-- Statistiques : effectif et taux de recouvrement --}}
                <div class="row g-2 mb-3">

                    <div class="col-6">
                        <div class="p-2 rounded-2 bg-light text-center">
                            <div class="fw-bold fs-5">{{ $classe->eleves_count }}</div>
                            <div class="text-muted" style="font-size:0.75rem">Élèves</div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="p-2 rounded-2 bg-light text-center">
                            <div class="fw-bold fs-5">
                                {{ number_format($classe->frais_scolarite, 0, ',', ' ') }}
                            </div>
                            <div class="text-muted" style="font-size:0.75rem">F CFA / an</div>
                        </div>
                    </div>

                </div>

                {{-- Barre de recouvrement des frais pour cette classe --}}
                @php
                    $attendus  = $classe->totalFraisAttendus();
                    $collectes = $classe->eleves->sum(fn($e) => $e->totalPaye());
                    $taux      = $attendus > 0
                        ? round(($collectes / $attendus) * 100, 1)
                        : 0;
                @endphp

                <div class="mb-1 d-flex justify-content-between small">
                    <span class="text-muted">Recouvrement</span>
                    <span class="fw-semibold">{{ $taux }}%</span>
                </div>
                <div class="progress" style="height:6px; border-radius:4px">
                    <div class="progress-bar
                        {{ $taux >= 75 ? 'bg-success' : ($taux >= 50 ? 'bg-warning' : 'bg-danger') }}"
                        style="width: {{ min(100, $taux) }}%">
                    </div>
                </div>

            </div>

            <div class="card-footer bg-white border-0 pb-3 d-flex gap-2">
                <a href="{{ route('eleves.index', ['classe_id' => $classe->id]) }}"
                   class="btn btn-sm btn-outline-secondary flex-grow-1">
                    <i class="bi bi-people me-1"></i> Élèves
                </a>
                <a href="{{ route('notes.index', ['classe_id' => $classe->id]) }}"
                   class="btn btn-sm btn-outline-primary flex-grow-1">
                    <i class="bi bi-journal-text me-1"></i> Notes
                </a>
                <a href="{{ route('classes.show', $classe) }}"
                    class="btn btn-sm btn-outline-secondary flex-grow-1">
                    <i class="bi bi-gear me-1"></i> Matières
                </a>
            </div>

        </div>
    </div>

    @empty
    {{-- Aucune classe créée : invitation à en créer une --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="text-muted">
                <i class="bi bi-building fs-1 d-block mb-3 opacity-25"></i>
                <p class="mb-3">Aucune classe créée pour le moment.</p>
                <a href="{{ route('classes.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Créer la première classe
                </a>
            </div>
        </div>
    </div>
    @endforelse

</div>

@endsection