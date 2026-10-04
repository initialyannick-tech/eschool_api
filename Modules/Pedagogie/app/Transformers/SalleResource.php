<?php

namespace Modules\Pedagogie\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'code'       => $this->code,
            'nom'        => $this->nom,
            'capacite'   => $this->capacite,
            'actif'      => $this->actif,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}