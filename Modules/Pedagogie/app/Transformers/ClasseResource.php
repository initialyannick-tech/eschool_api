<?php

namespace Modules\Pedagogie\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClasseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'capacite' => $this->capacite,
            'nombre_eleves' =>
                $this->whenLoaded(
                    'inscriptions',
                    fn () => $this->inscriptions
                        ->whereIn('statut', [
                            'affectee',
                            'confirmee',
                        ])
                        ->count()
                ),
            'places_disponibles' =>
                $this->whenLoaded(
                    'inscriptions',
                    fn () => max(
                        0,
                        $this->capacite -
                        $this->inscriptions
                            ->whereIn('statut', [
                                'affectee',
                                'confirmee',
                            ])
                            ->count()
                    )
                ),
            'cycle' => $this->whenLoaded('cycle'),
            'serie' => $this->whenLoaded('serie'),
            'annee_scolaire' => $this->whenLoaded('anneeScolaire'),
            'professeur_principal' => $this->whenLoaded('professeurPrincipal'),
            'description' => $this->description,
            'actif' => $this->actif,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
