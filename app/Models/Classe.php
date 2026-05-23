<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Classe extends Model
{
    use SoftDeletes, CascadeSoftDeletes;

    protected $fillable = ['nom', 'niveau', 'frais_scolarite', 'annee_scolaire', 'user_id'];
    
    // Une classe appartient à un enseignant
    public function enseignant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    // Suppression logique en cascade
    protected $cascadeDeletes = ['eleves', 'matieres'];

    // Une classe a plusieurs élèves
    public function eleves(): HasMany
    {
        return $this->hasMany(Eleve::class);
    }

    // Une classe a plusieurs matières
    public function matieres(): HasMany
    {
        return $this->hasMany(Matiere::class);
    }

    // Total des frais attendus = frais_scolarite × nombre d'élèves
    public function totalFraisAttendus(): float
    {
        return $this->frais_scolarite * $this->eleves()->count();
    }

    // gestion de l'unicité de nom de la classe pour la suppression logique
    protected static function booted(){
        static::deleting(function ($classe) {
            // Si ce n'est pas une suppression définitive (forceDelete)
            if (! $classe->isForceDeleting()) {
                $classe->nom = $classe->nom . '_deleted_' . time();
                $classe->save();
            }
        });
    }
}
