<?php

namespace Modules\Pedagogie\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatiereResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'libelle' => $this->libelle,
            'description' => $this->description,
            'actif' => (bool) $this->actif,
            'enseignants' => $this->whenLoaded('enseignants', function () {
                return $this->enseignants->map(function ($enseignant) {
                    return [
                        'id' => $enseignant->id,
                        'nom' => $enseignant->nom,
                        'prenom' => $enseignant->prenom,
                        'email' => $enseignant->email,
                    ];
                })->values();
            }),
        ];
    }
}
