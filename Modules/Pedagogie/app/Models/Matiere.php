<?php

namespace Modules\Pedagogie\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Admin\Models\User;
use Illuminate\Support\Str;

class Matiere extends Model
{
    use HasFactory;

    protected $table = 'matieres';

    protected $fillable = [
        'code',
        'libelle',
        'description',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

        protected static function boot()
    {
        parent::boot();
        static::creating(function ($matiere) {
            $libelle = trim($matiere->libelle);
            $libelleAscii = Str::ascii($libelle);
            $mots = preg_split('/\s+/', $libelleAscii);
            if (count($mots) === 1) {
                $prefixe = strtoupper(substr($mots[0], 0, 3));
            } else {
                $prefixe = '';
                foreach ($mots as $mot) {
                    if (!empty($mot)) {
                        $prefixe .= strtoupper(substr($mot, 0, 1));
                    }
                }
                $prefixe = substr($prefixe, 0, 3);
            }
            $dernier = Matiere::where('code', 'like', "{$prefixe}-%")->orderByDesc('id')->first();
            $numero = 1;
            if ($dernier) {
                preg_match('/-(\d+)$/', $dernier->code, $matches);
                if (isset($matches[1])) {
                    $numero = ((int) $matches[1]) + 1;
                }
            }
            $matiere->code = sprintf('%s-%03d',$prefixe,$numero);
        });
    }

    public function enseignants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enseignant_matiere', 'matiere_id', 'user_id');
    }
}
