<?php

namespace Modules\Communication\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Admin\Models\Role;
use Modules\Admin\Models\User;

class CommunicationMessage extends Model
{
    protected $fillable = [
        'sender_id',
        'title',
        'message',
        'audience',
        'target_role_id',
        'channels',
        'recipient_count',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'channels' => 'array',
            'recipient_count' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function targetRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'target_role_id');
    }
}
