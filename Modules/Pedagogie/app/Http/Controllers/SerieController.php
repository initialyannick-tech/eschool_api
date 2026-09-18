<?php

namespace Modules\Pedagogie\Http\Controllers;


use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Pedagogie\Repositories\SerieRepository;

class SerieController extends CoreController
{
    protected SerieRepository $repository;

    public function __construct(SerieRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Liste sans pagination.
     */
    public function list(): AnonymousResourceCollection
    {
        return $this->repository->index();
    }

    /**
     * Liste paginée.
     */
    public function paginate(): AnonymousResourceCollection
    {
        return $this->repository->paginate();
    }
}
