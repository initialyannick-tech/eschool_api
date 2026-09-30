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
            'lieu_travail'=> $this->lieu_travail,
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
                        'statut' => $eleve->statut,

                        'relation' => $eleve->pivot->relation ?? null,
                        'responsable_principal' => (bool) (
                            $eleve->pivot->responsable_principal ?? false
                        ),
                        'responsable_financier' => (bool) (
                            $eleve->pivot->responsable_financier ?? false
                        ),
                    ];
                })
            ),
            'observation' => $this->observation,
        ];
    }
}
