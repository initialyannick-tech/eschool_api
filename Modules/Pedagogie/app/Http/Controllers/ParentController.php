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
     * Liste parents sans pagination
     *
     * @return void
     */
    public function list() {
        return $this->repository->index();
    }

    /**
     * Liste des parents
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return $this->repository->paginate();
    }


    /**
     * Création d'un parents
     *
     * @param ParentRequest $request
     * @return JsonResponse
     */
    public function store(ParentRequest $request): JsonResponse
    {
        $data = $request->validated();
        $parents = $this->repository->store($data);
        if(!$parents){
            return $this->returnError('Une erreur est survenue lors de la création d\'un parent');
        } else {
            return $this->returnSuccess('Parent créé avec succès', $parents);
        }
    }


    /**
     * Afficher un parent
     *
     * @param [type] $code
     * @return ParentResource
     */
    public function show($code)
    {
        return $this->repository->show($code);
    }


    /**
     * Rechercher un parent
     *
     * @param [type] $keyword
     * @return AnonymousResourceCollection
     */
    public function search($keyword): AnonymousResourceCollection
    {
        return $this->repository->search($keyword);
    }


    /**
     * Mise à jour d'un parent
     *
     * @param ParentRequest $request
     * @param [type] $id
     * @return JsonResponse
     */
    public function update(ParentRequest $request, $id): JsonResponse
    {
        $data = $request->validated();
        $parents = $this->repository->update($id, $data);
        if(!$parents){
            return $this->returnError('Une erreur est survenue lors de la mise à jour du parent');
        } else {
            return $this->returnSuccess('Parent mis à jour avec succès', $parents);
        }
    }


    /**
     * Suppression d'un parent
     *
     * @param [type] $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $res = $this->repository->delete($id);
        if(!$res){
            return $this->returnError('Une erreur est survenue lors de la suppression du parent');
        } else {
            return $this->returnSuccess('Parent supprimé avec succès');
        }
    }
}
