<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Admin\Http\Requests\UserRequest;
use Modules\Admin\Http\Requests\UserUpdateRequest;
use Modules\Admin\Repositories\EnseignantRepository;
use Modules\Core\Http\Controllers\CoreController;

class EnseignantController extends CoreController
{
    protected EnseignantRepository $repository;

    public function __construct(EnseignantRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(): JsonResponse
    {
        return $this->returnSuccess('Liste des enseignants', $this->repository->index());
    }

    public function paginate(): JsonResponse
    {
        return $this->returnSuccess('Liste paginée des enseignants', $this->repository->paginate());
    }

    public function show(int $id): JsonResponse
    {
        try {
            $enseignant = $this->repository->show($id);
            return $this->returnSuccess('Enseignant récupéré', $enseignant);
        } catch (\Throwable $th) {
            return $this->returnError('Enseignant introuvable');
        }
    }

    public function store(UserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $res = $this->repository->store($data);

        if (!$res) {
            return $this->returnError('Erreur lors de la création de l\'enseignant');
        }

        return $this->returnSuccess('Enseignant créé avec succès', $res);
    }

    public function update(UserUpdateRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $res = $this->repository->update($data, $id);

        if (!$res) {
            return $this->returnError('Erreur lors de la mise à jour de l\'enseignant');
        }

        return $this->returnSuccess('Enseignant mis à jour avec succès', $res);
    }

    public function destroy(int $id): JsonResponse
    {
        $res = $this->repository->destroy($id);

        if (!$res) {
            return $this->returnError('Impossible de supprimer cet enseignant');
        }

        return $this->returnSuccess('Enseignant supprimé avec succès');
    }
}
