<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];

    // Un enseignant/gestionnaire est responsable de plusieurs classes
    public function classes(): HasMany
    {
        return $this->hasMany(Classe::class);
    }

    // Helpers pour vérifier le rôle facilement dans les vues et controllers
    public function isGestionnaire(): bool
    {
        return $this->role === 'gestionnaire';
    }

    public function isEnseignant(): bool
    {
        return $this->role === 'enseignant';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    protected static function booted(){
        static::deleting(function ($user) {
            // Si ce n'est pas une suppression définitive (forceDelete)
            if (! $user->isForceDeleting()) {
                $user->email = $user->email . '_deleted_' . time();
                $user->save();
            }
        });
    }
}
