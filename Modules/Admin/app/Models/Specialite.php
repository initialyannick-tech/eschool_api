<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Specialite extends Model
{
    use HasFactory;

    protected $table = 'specialites';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

}
