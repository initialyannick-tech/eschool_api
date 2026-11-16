<?php

namespace Modules\Communication\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Admin\Models\Role;
use Modules\Admin\Models\User;

class CommunicationMessage extends Model
{
    protected $fillable = [
        'auteur_id',
        'titre',
        'contenu',
        'audience',
        'role_cible_id',
        'canaux',
        'nombre_destinataires',
        'date_publication',
    ];

    protected function casts(): array
    {
        return [
            'canaux' => 'array',
            'nombre_destinataires' => 'integer',
            'date_publication' => 'datetime',
        ];
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auteur_id');
    }

    public function targetRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_cible_id');
    }
}
