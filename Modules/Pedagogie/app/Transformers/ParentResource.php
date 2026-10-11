<?php

namespace Modules\Pedagogie\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'telephone' => $this->telephone,
            'telephone_secondaire' => $this->telephone_secondaire,
            'email' => $this->email,
            'adresse' => $this->adresse,
            'profession'=> $this->profession,
            'statut' => $this->statut,
            'nombre_enfants' => $this->whenLoaded('eleves',fn () => $this->eleves->count()),
            'eleves' => $this->whenLoaded(
                'eleves',
                fn () => $this->eleves->map(function ($eleve) {
                    return [
                        'id' => $eleve->id,
                        'matricule' => $eleve->matricule,
                        'nom' => $eleve->nom,
                        'prenom' => $eleve->prenom,
                        'sexe' => $eleve->sexe,
                        'date_naissance' => $eleve->date_naissance?->format('Y-m-d'),
                        'lieu_naissance' => $eleve->lieu_naissance,
                        'nationalite' => $eleve->nationalite,
                        'photo' => $eleve->photo,
                        'statut' => $eleve->statut,
                    ];
                })
            ),
            'observation' => $this->observation,
        ];
    }
}
