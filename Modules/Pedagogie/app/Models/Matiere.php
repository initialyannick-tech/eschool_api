<?php

namespace Modules\Pedagogie\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Admin\Models\User;

class Matiere extends Model
{
    use HasFactory;

    protected $table = 'matieres';

    protected $fillable = [
        'code',
        'libelle',
        'description',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    public function enseignants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enseignant_matiere', 'matiere_id', 'user_id');
    }
}
