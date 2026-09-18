<?php

namespace Modules\Pedagogie\Repositories;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Pedagogie\Models\Serie;
use Modules\Pedagogie\Transformers\SerieResource;

class SerieRepository
{
    /**
     * Liste sans pagination.
     */
    public function index(): AnonymousResourceCollection
    {
        $series = Serie::orderBy('id', 'desc')->get();
        return SerieResource::collection($series);
    }

    /**
     * Liste paginée.
     */
    public function paginate(): AnonymousResourceCollection
    {
        $series = Serie::orderBy('id', 'desc')->paginate(10);
        return SerieResource::collection($series);
    }
}
