<?php

namespace Modules\Pedagogie\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
//use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;


class Eleve extends Model
{
    use HasFactory;

    protected $table = 'eleves';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'parent_id',
        'matricule',
        'nom',
        'prenom',
        'sexe',
        'date_naissance',
        'lieu_naissance',
        'nationalite',
        'adresse',
        'telephone',
        'email',
        'photo',
        'situation_particuliere',
        'statut',
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($eleve) {

            $anneeNaissance = \Carbon\Carbon::parse($eleve->date_naissance)->format('Y');
            $initialeNom = strtoupper(substr(Str::ascii(trim($eleve->nom)), 0, 1));
            $initialePrenom = strtoupper( substr(Str::ascii(trim($eleve->prenom)), 0, 1));
            $dernier = Eleve::where('matricule', 'like', "{$anneeNaissance}-{$initialeNom}{$initialePrenom}-%")->orderByDesc('id')->first();
            $numero = 1;
            if ($dernier) {
                preg_match('/-(\d+)$/', $dernier->matricule, $matches);
                if (isset($matches[1])) {
                    $numero = ((int) $matches[1]) + 1;
                }
            }
            $eleve->matricule = sprintf('%s-%s%s-%03d', $anneeNaissance,$initialeNom, $initialePrenom, $numero
            );
        });
    }

    /**
     * Un élève appartient à un seul parent/tuteur.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    /**
     * Un élève peut avoir plusieurs inscriptions
     * au cours de sa scolarité.
     */
   /* public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class, 'eleve_id');
    }*/
}
