
@extends('layouts.app')

@section('title', 'Élèves')
@section('page-title', 'Gestion des élèves')

{{-- Bouton "Inscrire un élève" dans l'en-tête de page --}}
@section('page-action')
    <a href="{{ route('eleves.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Inscrire un élève
    </a>
@endsection

@section('content')

{{-- Barre de recherche par nom --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('eleves.index') }}" class="d-flex gap-2">
            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Rechercher par nom ou prénom..."
                value="{{ request('search') }}"
                {{-- request('search') : conserve la valeur saisie après soumission --}}
            >
            <button type="submit" class="btn btn-outline-secondary">
                <i class="bi bi-search"></i>
            </button>
            {{-- Bouton reset : efface la recherche --}}
            @if(request('search'))
                <a href="{{ route('eleves.index') }}" class="btn btn-outline-danger">
                    <i class="bi bi-x"></i>
                </a>
            @endif
        </form>
    </div>
</div>

{{-- Tableau des élèves --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:48px"></th> {{-- Colonne photo --}}
                    <th>Élève</th>
                    <th>Classe</th>
                    <th>Parent / Tuteur</th>
                    <th class="text-end">Frais restants</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>

                {{-- @forelse : comme @foreach mais gère le cas "liste vide" avec @empty --}}
                @forelse($eleves as $eleve)
                <tr>
                    {{-- Photo de l'élève ou initiales si pas de photo --}}
                    <td>
                        @if($eleve->photo)
                            <img
                                src="{{ Storage::url($eleve->photo) }}"
                                alt="{{ $eleve->nom_complet }}"
                                class="rounded-circle"
                                style="width:38px; height:38px; object-fit:cover"
                            >
                        @else
                            {{-- Avatar avec initiales si pas de photo --}}
                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center
                                        justify-content-center text-primary fw-bold"
                                 style="width:38px; height:38px; font-size:0.8rem">
                                {{-- substr(0,1) prend la 1ère lettre du prénom et du nom --}}
                                {{ strtoupper(substr($eleve->prenom, 0, 1) . substr($eleve->nom, 0, 1)) }}
                            </div>
                        @endif
                    </td>

                    <td>
                        <div class="fw-semibold">{{ $eleve->nom_complet }}</div>
                        {{-- Accessor défini dans le modèle Eleve --}}
                        <div class="text-muted small">
                            Né(e) le {{ $eleve->date_naissance->format('d/m/Y') }}
                        </div>
                    </td>

                    <td>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary fw-normal">
                            {{ $eleve->classe->nom }}
                        </span>
                    </td>

                    <td>
                        <div>{{ $eleve->nom_parent ?? '—' }}</div>
                        <div class="text-muted small">{{ $eleve->telephone_parent ?? '' }}</div>
                    </td>

                    {{-- Reste à payer avec coloration selon le montant --}}
                    <td class="text-end">
                        @php $reste = $eleve->resteAPayer(); @endphp
                        @if($reste <= 0)
                            {{-- Tout est payé --}}
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle me-1"></i> Soldé
                            </span>
                        @else
                            <span class="text-danger fw-semibold">
                                {{ number_format($reste, 0, ',', ' ') }} F
                            </span>
                        @endif
                    </td>

                    {{-- Boutons d'action : modifier et supprimer --}}
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">

                            {{-- Bouton modifier --}}
                            <a href="{{ route('eleves.edit', $eleve) }}"
                               class="btn btn-sm btn-outline-primary"
                               title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>

                            {{-- Bouton supprimer : formulaire DELETE car HTML ne supporte
                                 pas la méthode DELETE nativement, on utilise @method('DELETE') --}}
                            <form method="POST" action="{{ route('eleves.destroy', $eleve) }}"
                                  onsubmit="return confirm('Supprimer cet élève ? Cette action est irréversible.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>

                {{-- Cas où la liste est vide (aucun élève ou recherche sans résultat) --}}
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>
                        @if(request('search'))
                            Aucun élève trouvé pour "{{ request('search') }}"
                        @else
                            Aucun élève inscrit pour le moment.
                        @endif
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    {{-- Pagination Bootstrap générée automatiquement par Laravel --}}
    @if($eleves->hasPages())
        <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center">
            <small class="text-muted">
                {{-- links() avec withQueryString() conserve le filtre de recherche dans les liens de pagination --}}
                Affichage de {{ $eleves->firstItem() }} à {{ $eleves->lastItem() }}
                sur {{ $eleves->total() }} élèves
            </small>
            {{ $eleves->withQueryString()->links() }}
        </div>
    @endif

</div>

@endsection