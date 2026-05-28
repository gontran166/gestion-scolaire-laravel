
@extends('layouts.app')

@section('title', 'Classement')
@section('page-title', 'Classement — ' . $classe->nom)

@section('page-action')
    <a href="{{ route('notes.index', ['classe_id' => $classe->id]) }}"
       class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour aux notes
    </a>
@endsection

@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div>
            <h6 class="fw-semibold mb-0">
                <i class="bi bi-trophy-fill text-warning me-2"></i>
                Trimestre {{ $trimestre }} — {{ $annee }}
            </h6>
            <small class="text-muted">{{ count($classement) }} élèves classés</small>
        </div>
    </div>

    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width:80px">Rang</th>
                    <th>Élève</th>
                    <th class="text-center">Moyenne /10</th>
                    <th>Appréciation</th>
                </tr>
            </thead>
            <tbody>
                {{-- $classement est le tableau retourné par MoyenneService::calculerClassement() --}}
                @foreach($classement as $item)
                <tr class="{{ $item['rang'] <= 3 ? 'table-warning' : '' }}">

                    {{-- Médaille pour le podium, numéro sinon --}}
                    <td class="text-center fw-bold">
                        @if($item['rang'] === 1)
                            <span title="1er" style="font-size:1.4rem">🥇</span>
                        @elseif($item['rang'] === 2)
                            <span title="2ème" style="font-size:1.4rem">🥈</span>
                        @elseif($item['rang'] === 3)
                            <span title="3ème" style="font-size:1.4rem">🥉</span>
                        @else
                            <span class="text-muted">{{ $item['rang'] }}</span>
                        @endif
                    </td>

                    <td class="fw-semibold">{{ $item['eleve']->nom_complet }}</td>

                    {{-- Moyenne mise en forme et colorée --}}
                    <td class="text-center">
                        @if($item['moyenne'] !== null)
                            <span class="badge fs-6 bg-{{ $item['moyenne'] >= 8 ? 'success' : ($item['moyenne'] >= 5 ? 'primary' : 'danger') }}">
                                {{ number_format($item['moyenne'], 2) }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    {{-- Appréciation textuelle selon la moyenne --}}
                    <td>
                        @if($item['moyenne'] !== null)
                            @php
                                $moy = $item['moyenne'];
                                $appreciation = match(true) {
                                    $moy >= 9 => ['Excellent',     'success'],
                                    $moy >= 8 => ['Très bien',     'success'],
                                    $moy >= 7 => ['Bien',          'primary'],
                                    $moy >= 6 => ['Assez bien',    'info'],
                                    $moy >= 5 => ['Passable',      'warning'],
                                    default    => ['Insuffisant',   'danger'],
                                };
                            @endphp
                            <span class="text-{{ $appreciation[1] }} fw-semibold">
                                {{ $appreciation[0] }}
                            </span>
                        @endif
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection