<?php

namespace Modules\Communication\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Admin\Models\User;

class ConversationMessage extends Model
{
    protected $fillable = [
        'conversation_id',
        'auteur_id',
        'contenu',
        'piece_jointe_chemin',
        'piece_jointe_nom',
        'piece_jointe_mime',
        'piece_jointe_taille',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auteur_id');
    }
}
