<?php

namespace Modules\Pedagogie\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Eleve extends Model
{
    use HasFactory;

    protected $table = 'eleves';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'parent_id',
        'matricule',
        'nom',
        'prenom',
        'sexe',
        'date_naissance',
        'lieu_naissance',
        'nationalite',
        'adresse',
        'telephone',
        'email',
        'photo',
        'situation_particuliere',
        'statut',
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    /**
     * Un élève appartient à un seul parent/tuteur.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    /**
     * Un élève peut avoir plusieurs inscriptions
     * au cours de sa scolarité.
     */
   /* public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class, 'eleve_id');
    }*/
}
