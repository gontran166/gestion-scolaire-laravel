<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Eleve extends Model
{
    use SoftDeletes, CascadeSoftDeletes;
    
    protected $fillable = [
        'nom', 'prenom', 'date_naissance',
        'photo', 'nom_parent', 'telephone_parent', 'classe_id'
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    // Suppression logique en cascade
    protected $cascadeDeletes = ['paiements', 'notes'];

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    // Total déjà payé par cet élève
    public function totalPaye(): float
    {
        return $this->paiements()->sum('montant');
    }

    // Reste à payer = frais de la classe - total payé
    public function resteAPayer(): float
    {
        return max(0, $this->classe->frais_scolarite - $this->totalPaye());
    }

    // Nom complet formaté
    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }
}
