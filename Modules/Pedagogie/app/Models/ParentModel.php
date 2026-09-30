<?php

namespace Modules\Pedagogie\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;


class ParentModel extends Model
{
    use HasFactory;

    protected $table = 'parents';
    /**
     * The attributes that are mass assignable.
     */
     protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'telephone_secondaire',
        'email',
        'adresse',
        'profession',
        'lieu_travail',
        'statut',
        'observation',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Élèves associés à ce parent.
     */
    public function eleves(): BelongsToMany
    {
        return $this->belongsToMany(
            Eleve::class,
            'eleve_parent',
            'parent_id',
            'eleve_id'
        )->withPivot([
            'relation',
            'responsable_principal',
            'responsable_financier',
        ])->withTimestamps();
    }
  
}
