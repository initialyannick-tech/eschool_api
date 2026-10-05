<?php

namespace Modules\Pedagogie\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EleveParent extends Model
{
    use HasFactory;

    protected $table = 'eleve_parent';

    protected $fillable = [
        'eleve_id',
        'parent_id',
        'relation',
        'responsable_principal',
        'responsable_financier',
    ];

    protected $casts = [
        'responsable_principal' => 'boolean',
        'responsable_financier' => 'boolean',
    ];

    /**
     * Élève concerné par la relation.
     */
    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class);
    }

    /**
     * Parent concerné par la relation.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentModel::class);
    }
}
