<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;


class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'libelle',
        'code',
        'description'
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($role) {
            $role->code = Str::slug($role->libelle);
        });
        static::updating(function ($role) {
            $role->code = Str::slug($role->libelle);
        });
    }

    public function permissions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

}
