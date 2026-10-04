<?php

namespace Modules\Pedagogie\app\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmploiDuTempsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'annee_scolaire_id' => $this->annee_scolaire_id,
            'classe'            => new ClasseResource($this->whenLoaded('classe')),
            'matiere_id'        => $this->matiere_id,
            'enseignant_id'     => $this->enseignant_id,
            'salle'             => new SalleResource($this->whenLoaded('salle')),
            'jour_semaine'      => $this->jour_semaine,
            'heure_debut'       => $this->heure_debut,
            'heure_fin'         => $this->heure_fin,
            'type_cours'        => $this->type_cours,
            'actif'             => $this->actif,
            'created_at'        => $this->created_at?->toDateTimeString(),
        ];
    }
}