<?php

namespace Modules\Parametre\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AnneeScolaire extends Model
{
    use HasFactory;

    protected $table = 'annees_scolaires';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'libelle',
        'annee_debut',
        'annee_fin',
        'statut',
        'active',
        'date_ouverture',
        'date_cloture',
        'description',
    ];

    protected $casts = [
        'annee_debut' => 'integer',
        'annee_fin' => 'integer',
        'active' => 'boolean',
        'date_ouverture' => 'date',
        'date_cloture' => 'date',
    ];

}
