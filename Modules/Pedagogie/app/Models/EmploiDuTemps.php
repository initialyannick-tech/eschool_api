<?php

namespace Modules\Pedagogie\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User; // Ajustez le namespace selon l'emplacement du modèle User

class EmploiDuTemps extends Model
{
    use HasFactory;

    protected $table = 'emplois_du_temps';

    protected $fillable = [
        'annee_scolaire_id',
        'classe_id',
        'matiere_id',
        'enseignant_id',
        'salle_id',
        'jour_semaine',
        'heure_debut',
        'heure_fin',
        'type_cours',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    // Relations
    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    public function enseignant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enseignant_id');
    }

    public function salle(): BelongsTo
    {
        return $this->belongsTo(Salle::class, 'salle_id');
    }
}