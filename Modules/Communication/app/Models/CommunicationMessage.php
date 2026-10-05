<?php

namespace Modules\Communication\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Admin\Models\Role;
use Modules\Admin\Models\User;
use Modules\Communication\Database\Factories\CommunicationMessageFactory;

class CommunicationMessage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sender_id',
        'role_id',
        'title',
        'message',
        'audience',
        'channels',
        'recipient_count',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'channels' => 'array',
            'sent_at' => 'datetime',
        ];
    }

    protected static function newFactory(): CommunicationMessageFactory
    {
        return CommunicationMessageFactory::new();
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
