<?php

namespace Modules\Pedagogie\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Admin\Models\User;
use Modules\Parametre\Models\AnneeScolaire;

class Classe extends Model
{
    use HasFactory;

    protected $table = 'classes';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'annee_scolaire_id',
        'cycle_id',
        'serie_id',
        'professeur_principal_id',
        'nom',
        'capacite',
        'description',
        'actif',
    ];

    protected $casts = [
        'capacite' => 'integer',
        'actif' => 'boolean',
    ];

    /**
     * Année scolaire.
     */
    public function anneeScolaire(): BelongsTo
    {
        return $this->belongsTo(AnneeScolaire::class, 'annee_scolaire_id');
    }

    /**
     * Cycle.
     */
    public function cycle(): BelongsTo
    {
        return $this->belongsTo(Cycle::class, 'cycle_id'
        );
    }

    /**
     * Série.
     */
    public function serie(): BelongsTo
    {
        return $this->belongsTo(Serie::class, 'serie_id'
        );
    }

    /**
     * Professeur principal.
     */
    public function professeurPrincipal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professeur_principal_id'
        );
    }

    /**
     * Inscriptions des élèves.
     */
    /*public function inscriptions(): HasMany
    {
        return $this->hasMany(
            Inscription::class,
            'classe_id'
        );
    }*/
}
