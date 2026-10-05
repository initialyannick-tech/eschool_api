<?php

namespace Modules\Pedagogie\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Admin\Models\User;

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
        'user_id',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

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
