<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table="users";

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
    ];

    const ACTIVE = 'active';
    const INACTIVE = 'inactive';


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


    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

}
