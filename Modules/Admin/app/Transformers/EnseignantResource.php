<?php

namespace Modules\Admin\Transformers;
use Illuminate\Http\Resources\Json\JsonResource;

class EnseignantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'status' => $this->status,
            'specialite_id' => $this->specialite_id,
            'specialite' => $this->specialite ? $this->specialite->only(['id', 'nom']) : null,
            'matieres' => $this->matieres?->map(function ($matiere) {
                return [
                    'id' => $matiere->id,
                    'code' => $matiere->code,
                    'libelle' => $matiere->libelle,
                ];
            })->values()->all() ?? [],
            'matiere_count' => $this->matieres?->count() ?? 0,
        ];
    }
}
