<?php

namespace Modules\Pedagogie\Http\Controllers;



use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Pedagogie\Http\Requests\ParentRequest;
use Modules\Pedagogie\Repositories\ParentRepository;
use Modules\Pedagogie\Transformers\ParentResource;

class ParentController extends CoreController
{
    protected ParentRepository $repository;

    public function __construct(ParentRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Liste complète des parents
     */
    public function list(): AnonymousResourceCollection
    {
        return $this->repository->index();
    }

    /**
     * Liste paginée des parents
     */
    public function paginate(): AnonymousResourceCollection
    {
        return $this->repository->paginate();
    }

    /**
     * Afficher un parent
     */
    public function show(int $id): ParentResource
    {
        return $this->repository->show($id);
    }

    /**
     * Créer un parent
     */
    public function store(ParentRequest $request): JsonResponse {

        $parent = $this->repository->store($request->validated());
        return $this->returnSuccess('Parent/tuteur créé avec succès.', new ParentResource($parent->load('eleves')));
    }

    /**
     * Modifier un parent
     */
    public function update(ParentRequest $request, int $id): JsonResponse {
        $parent = $this->repository->update($request->validated(), $id);
        return $this->returnSuccess('Parent/tuteur modifié avec succès.', new ParentResource($parent));
    }

    /**
     * Supprimer un parent
     */
    public function destroy(int $id): JsonResponse {
        $this->repository->destroy($id);
        return $this->returnSuccess('Parent/tuteur supprimé avec succès.');
    }

    /**
     * Liste des enfants d'un parent
     */
    public function eleves(int $parentId): AnonymousResourceCollection {
        return $this->repository->eleves($parentId);
    }
}
