<?php

namespace Modules\Admin\Http\Controllers;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Admin\Http\Requests\RoleRequest;
use Modules\Admin\Repositories\RoleRepository;
use Modules\Core\Http\Controllers\CoreController;

class RoleController extends CoreController
{
    protected RoleRepository $repository;

    public function __construct(RoleRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Liste paginée des rôles
     *
     * @return AnonymousResourceCollection
     */
    public function paginate(): AnonymousResourceCollection
    {
        return $this->repository->paginate();
    }

    /**
     * Liste des rôles
     *
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        return $this->repository->liste();
    }


    /**
     * Liste des permissions
     *
     * @return Collection
     */
    public function permissions()
    {
        return $this->repository->permissions();
    }


    /**
     * Créer un rôle
     *
     * @param RoleRequest $request
     * @return JsonResponse
     */
    public function store(RoleRequest $request): JsonResponse
    {
        $data = $request->validated();
        $res = $this->repository->store($data);
        if(!$res) {
            return $this->returnError('Erreur lors de la création du rôle');
        } else {
            return $this->returnSuccess('Rôle créé avec succès', $res);
        }
    }


    /**
     * Modification d'un rôle
     *
     * @param RoleRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(RoleRequest $request, $id): JsonResponse
    {
        $data = $request->validated();
        $res = $this->repository->update($data, $id);
        if(!$res) {
            return $this->returnError('Erreur lors de la modification du rôle');
        } else {
            return $this->returnSuccess('Rôle modifié avec succès', $res);
        }
    }

    /**
     * Supprimer un rôle
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $res = $this->repository->destroy($id);
        if(!$res) {
            return $this->returnError('Ce rôle est utilisé par un ou plusieurs utilisateurs');
        } else {
            return $this->returnSuccess('Rôle supprimé avec succès');
        }
    }
}
