<?php

namespace Modules\Pedagogie\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EleveResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'matricule' => $this->matricule,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'nom_complet' => trim($this->prenom . ' ' . $this->nom),
            'sexe' => $this->sexe,
            'date_naissance' => $this->date_naissance ? $this->date_naissance->format('Y-m-d') : null,
            'lieu_naissance' => $this->lieu_naissance,
            'nationalite' => $this->nationalite,
            'adresse' => $this->adresse,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'photo' => $this->photo,
            'situation_particuliere' => $this->situation_particuliere,
            'statut' => $this->statut,
            'parent' => $this->whenLoaded(
                'parent',
                function () {
                    if (!$this->parent) {return null;}
                    return [
                        'id' => $this->parent->id,
                        'nom' => $this->parent->nom,
                        'prenom' => $this->parent->prenom,
                        'relation' => $this->parent->relation,
                        'telephone' => $this->parent->telephone,
                        'email' => $this->parent->email,
                        'adresse' => $this->parent->adresse,
                        'responsable_principal' => $this->parent->responsable_principal,
                        'responsable_financier' => $this->parent->responsable_financier,
                    ];
                }
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
