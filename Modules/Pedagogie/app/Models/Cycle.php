<?php

namespace Modules\Pedagogie\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Cycle extends Model
{
    use HasFactory;

    protected $table = 'cycles';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'code',
        'libelle',
        'description',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    public function classes(): HasMany
    {
        return $this->hasMany(Classe::class, 'cycle_id');
    }


}
