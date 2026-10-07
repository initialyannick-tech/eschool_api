<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Communication\Models\Conversation;
use Modules\Pedagogie\Models\Eleve;
use Modules\Pedagogie\Models\Matiere;
use Modules\Pedagogie\Models\ParentModel;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'password_changed',
        'status',
        'role_id',
        'specialite_id',
    ];

    const ACTIVE = 'active';

    const INACTIVE = 'inactive';

    const ENSEIGNANT = '4';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->password = bcrypt($user->password);
            $user->status = self::ACTIVE;
            $user->nom = strtoupper($user->nom);
            $user->prenom = ucwords($user->prenom);
        });

        static::updating(function ($user) {
            $user->nom = strtoupper($user->nom);
            $user->prenom = ucwords($user->prenom);
            if ($user->isDirty('password')) {
                $user->password = bcrypt($user->password);
            }
        });
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function specialite(): BelongsTo
    {
        return $this->belongsTo(Specialite::class);
    }

    public function matieres(): BelongsToMany
    {
        return $this->belongsToMany(Matiere::class, 'enseignant_matiere', 'user_id', 'matiere_id');
    }

    public function parentProfile(): HasOne
    {
        return $this->hasOne(ParentModel::class, 'user_id');
    }

    public function eleveProfile(): HasOne
    {
        return $this->hasOne(Eleve::class, 'user_id');
    }

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants')
            ->withPivot(['last_read_at', 'archived_at'])
            ->withTimestamps();
    }
}
