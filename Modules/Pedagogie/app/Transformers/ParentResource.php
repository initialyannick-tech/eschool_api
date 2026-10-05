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
            'user_id' => $this->user_id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'telephone' => $this->telephone,
            'telephone_secondaire' => $this->telephone_secondaire,
            'email' => $this->email,
            'adresse' => $this->adresse,
            'profession'=> $this->profession,
            'lieu_travail'=> $this->lieu_travail,
            'statut' => $this->statut,
            'compte' => $this->whenLoaded('user', fn () => $this->user ? [
                'id' => $this->user->id,
                'email' => $this->user->email,
                'role_id' => $this->user->role_id,
            ] : null),
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
