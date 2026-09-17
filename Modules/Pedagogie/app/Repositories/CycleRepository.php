<?php

namespace Modules\Pedagogie\Repositories;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Pedagogie\Models\Cycle;
use Modules\Pedagogie\Transformers\CycleResource;

class CycleRepository
{
    /**
     * Liste des cycle sans pagination.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $cycles = Cycle::orderBy('id', 'desc')->get();
        return CycleResource::collection($cycles);
    }

    /**
     * Liste paginée des cycles.
     *
     * @return AnonymousResourceCollection
     */
    public function paginate(): AnonymousResourceCollection
    {
        $cycles = Cycle::orderBy('id', 'desc')->paginate(10);
        return CycleResource::collection($cycles);
    }
}
