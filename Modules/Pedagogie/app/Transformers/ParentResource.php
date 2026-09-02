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
            'relation' => $this->relation,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'adresse' => $this->adresse,
            'responsable_principal' => $this->responsable_principal,
            'responsable_financier' => $this->responsable_financier,
            'nombre_enfants' => $this->whenLoaded('eleves', fn () => $this->eleves->count()),
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
                    ];
                })
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
