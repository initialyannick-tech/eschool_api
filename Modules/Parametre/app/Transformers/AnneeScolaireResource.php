<?php

namespace Modules\Parametre\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnneeScolaireResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'libelle' => $this->libelle,
            'annee_debut' => $this->annee_debut,
            'annee_fin' => $this->annee_fin,
            'statut' => $this->statut,
            'active' => $this->active,
            'date_ouverture' => $this->date_ouverture?->format('Y-m-d'),
            'date_cloture' => $this->date_cloture?->format('Y-m-d'),
            'description' => $this->description,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
