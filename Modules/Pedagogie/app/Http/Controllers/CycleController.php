<?php

namespace Modules\Pedagogie\Http\Controllers;


use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Pedagogie\Repositories\CycleRepository;

class CycleController extends CoreController
{
    protected CycleRepository $repository;

    public function __construct(CycleRepository $repository)
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
