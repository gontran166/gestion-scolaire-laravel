@extends('layouts.app')

@section('title', 'Élèves en impayé')
@section('page-title', 'Élèves en situation d\'impayé')

@section('page-action')
    {{-- Nombre total d'impayés affiché dans l'en-tête --}}
    <span class="badge bg-danger fs-6 px-3 py-2">
        {{ $eleves->count() }} élève(s) concerné(s)
    </span>
@endsection

@section('content')

@if($eleves->isEmpty())
    {{-- Aucun impayé : message positif --}}
    <div class="card border-0 shadow-sm text-center py-5">
        <div>
            <i class="bi bi-check-circle-fill text-success" style="font-size:3rem"></i>
            <h5 class="mt-3 fw-semibold">Aucun impayé !</h5>
            <p class="text-muted">
                Tous les élèves sont à jour dans leurs paiements.
            </p>
        </div>
    </div>

@else

    {{-- Résumé financier global des impayés --}}
    {{-- Calcul du total non encaissé sur tous les élèves en impayé --}}
    @php
        $totalImpaye = $eleves->sum(fn($e) => $e->resteAPayer());
    @endphp

    <div class="alert alert-danger d-flex align-items-center gap-3 mb-4">
        <i class="bi bi-exclamation-triangle-fill fs-3 flex-shrink-0"></i>
        <div>
            <strong>Total non encaissé :</strong>
            {{ number_format($totalImpaye, 0, ',', ' ') }} F CFA
            sur {{ $eleves->count() }} élève(s).
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Élève</th>
                        <th>Classe</th>
                        <th class="text-end">Frais annuels</th>
                        <th class="text-end">Total payé</th>
                        <th class="text-end">Reste dû</th>
                        <th>Avancement</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- $eleves est déjà trié par resteAPayer() décroissant dans le controller --}}
                    @foreach($eleves as $index => $eleve)
                    @php
                        $frais   = $eleve->classe->frais_scolarite;
                        $paye    = $eleve->totalPaye();
                        $reste   = $eleve->resteAPayer();
                        $pct     = $frais > 0 ? round(($paye / $frais) * 100) : 0;
                    @endphp
                    <tr>
                        {{-- Numéro de ligne --}}
                        <td class="text-muted small">{{ $index + 1 }}</td>

                        {{-- Nom de l'élève --}}
                        <td>
                            <div class="fw-semibold">{{ $eleve->nom_complet }}</div>
                            @if($eleve->telephone_parent)
                                {{-- Numéro parent cliquable pour appel mobile --}}
                                <a href="tel:{{ $eleve->telephone_parent }}"
                                   class="text-muted small text-decoration-none">
                                    <i class="bi bi-telephone me-1"></i>
                                    {{ $eleve->telephone_parent }}
                                </a>
                            @endif
                        </td>

                        <td>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary fw-normal">
                                {{ $eleve->classe->nom }}
                            </span>
                        </td>

                        <td class="text-end text-muted">
                            {{ number_format($frais, 0, ',', ' ') }} F
                        </td>

                        <td class="text-end text-success fw-semibold">
                            {{ number_format($paye, 0, ',', ' ') }} F
                        </td>

                        {{-- Reste dû : coloré en orange si > 50% dû, rouge si presque rien payé --}}
                        <td class="text-end fw-bold
                            {{ $pct < 25 ? 'text-danger' : 'text-warning' }}">
                            {{ number_format($reste, 0, ',', ' ') }} F
                        </td>

                        {{-- Barre de progression compacte --}}
                        <td style="min-width:100px">
                            <div class="progress" style="height:6px">
                                <div class="progress-bar
                                    {{ $pct >= 75 ? 'bg-success' : ($pct >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                    style="width:{{ $pct }}%"
                                    title="{{ $pct }}% payé">
                                </div>
                            </div>
                            <small class="text-muted">{{ $pct }}%</small>
                        </td>

                        {{-- Bouton d'action rapide : enregistrer un paiement pour cet élève --}}
                        <td class="text-center">
                            <a href="{{ route('paiements.create', ['eleve_id' => $eleve->id]) }}"
                               class="btn btn-sm btn-outline-success"
                               title="Enregistrer un paiement">
                                <i class="bi bi-cash-coin me-1"></i> Payer
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

                {{-- Ligne de total en pied de tableau --}}
                <tfoot class="table-light fw-semibold">
                    <tr>
                        <td colspan="4" class="text-end">Total restant à encaisser :</td>
                        <td></td>
                        <td class="text-end text-danger fw-bold">
                            {{ number_format($totalImpaye, 0, ',', ' ') }} F CFA
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>

            </table>
        </div>
    </div>

@endif

@endsection