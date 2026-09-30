<?php

namespace Modules\Pedagogie\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EleveParentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'eleve_id' => $this->eleve_id,
            'parent_id' => $this->parent_id,
            'relation' => $this->relation,
            'responsable_principal' => (bool) $this->responsable_principal,
            'responsable_financier' => (bool) $this->responsable_financier,
            'eleve' => new EleveResource(
                $this->whenLoaded('eleve')
            ),
            'parent' => new ParentResource(
                $this->whenLoaded('parent')
            ),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
