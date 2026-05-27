<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\EleveController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnseignantController;

// Authentification (accessible sans être connecté)
Route::get('/connexion', [AuthController::class, 'showLogin'])->name('connexion');
Route::post('/connexion', [AuthController::class, 'connexion']);
Route::post('/deconnexion', [AuthController::class, 'deconnexion'])->name('deconnexion')->middleware('auth');

// Toutes les routes suivantes nécessitent d'être connecté
Route::middleware('auth')->group(function () {

    // Tableau de bord (commun aux deux rôles, contenu adapté dans le controller)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Gestion pédagogique (enseignant + gestionnaire)
    Route::get('/notes', [NoteController::class, 'index'])->name('notes.index');
    Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::get('/classes/{classe}/classement', [NoteController::class, 'classement'])->name('notes.classement');

    // Gestion de changement de mot de passe d'un enseignant
    Route::get('/profil/mot-de-passe', [\App\Http\Controllers\ProfilController::class, 'showChangePassword'])
     ->name('profil.password.edit');
    Route::put('/profil/mot-de-passe', [\App\Http\Controllers\ProfilController::class, 'updatePassword'])
        ->name('profil.password.update');

    // Gestion administrative (gestionnaire uniquement)
    Route::middleware('role:gestionnaire')->group(function () {

        Route::resource('classes', ClasseController::class)->parameters(['classes' => 'classe' ]);

        // Gestion des matières d'une classe
        Route::post('classes/{classe}/matieres',
            [\App\Http\Controllers\ClasseController::class, 'storeMatieres'])
            ->name('classes.matieres.store');
        Route::delete('classes/{classe}/matieres/{matiere}',
            [\App\Http\Controllers\ClasseController::class, 'destroyMatiere'])
            ->name('classes.matieres.destroy');

        Route::resource('eleves', EleveController::class)->parameters(['eleves'=>'eleve']);

        Route::get('/paiements/create', [PaiementController::class, 'create'])->name('paiements.create');
        Route::post('/paiements', [PaiementController::class, 'store'])->name('paiements.store');
        Route::get('/paiements/{paiement}/recu', [PaiementController::class, 'recu'])->name('paiements.recu');

        Route::get('/dashboard/impayes', [DashboardController::class, 'impayes'])->name('dashboard.impayes');

        // Gestion des enseignants (gestionnaire uniquement)
        Route::resource('enseignants', EnseignantController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

        // Affectation rapide d'une classe à un enseignant depuis le dashboard
        Route::post('/enseignants/{enseignant}/affecter-classe',
            [EnseignantController::class, 'affecterClasse'])
            ->name('enseignants.affecter-classe');
    });
});

// Redirection de la racine vers dashboard
Route::get('/', fn() => redirect()->route('connexion'));
