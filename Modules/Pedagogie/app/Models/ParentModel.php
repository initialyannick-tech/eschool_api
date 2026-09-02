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
        'relation',
        'telephone',
        'email',
        'adresse',
        'responsable_principal',
        'responsable_financier',
    ];
    protected $casts = [
        'responsable_principal' => 'boolean',
        'responsable_financier' => 'boolean',
    ];

    /**
     * Un parent peut avoir plusieurs élèves.
     */
    public function eleves(): HasMany
    {
        return $this->hasMany(Eleve::class, 'parent_id');
    }
}
