<?php

namespace Modules\Pedagogie\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Salle extends Model
{
    use HasFactory;

    protected $table = 'salles';

    protected $fillable = [
        'code',
        'nom',
        'capacite',
        'actif',
    ];

    protected $casts = [
        'capacite' => 'integer',
        'actif'    => 'boolean',
    ];

    public function emploisDuTemps(): HasMany
    {
        return $this->hasMany(EmploiDuTemps::class, 'salle_id');
    }
}