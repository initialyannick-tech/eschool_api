<?php

namespace Modules\Pedagogie\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Pedagogie\Http\Requests\MatiereRequest;
use Modules\Pedagogie\Repositories\MatiereRepository;

class MatiereController extends CoreController
{
    protected MatiereRepository $repository;

    public function __construct(MatiereRepository $repository)
    {
        $this->repository = $repository;
    }

    public function list(): AnonymousResourceCollection
    {
        return $this->repository->index();
    }

    public function paginate(): AnonymousResourceCollection
    {
        return $this->repository->paginate();
    }

    public function show(int $id): JsonResponse
    {
        $matiere = $this->repository->show($id);
        if (!$matiere) {
            return $this->returnError('Matière introuvable');
        }

        return $this->returnSuccess('Matière récupérée avec succès', $matiere);
    }

    public function store(MatiereRequest $request): JsonResponse
    {
        $data = $request->validated();
        $res = $this->repository->store($data);

        if (!$res) {
            return $this->returnError('Une matière avec ce code existe déjà.');
        }

        return $this->returnSuccess('Matière créée avec succès', $res);
    }

    public function update(MatiereRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $res = $this->repository->update($data, $id);

        if (!$res) {
            return $this->returnError('Erreur lors de la mise à jour de la matière.');
        }

        return $this->returnSuccess('Matière mise à jour avec succès', $res);
    }

    public function destroy(int $id): JsonResponse
    {
        $res = $this->repository->destroy($id);

        if (!$res) {
            return $this->returnError('Impossible de supprimer cette matière.');
        }

        return $this->returnSuccess('Matière supprimée avec succès');
    }

    public function enseignants(int $id): JsonResponse
    {
        $enseignants = $this->repository->enseignants($id);
        return $this->returnSuccess('Liste des enseignants de la matière', $enseignants);
    }

    public function assignerEnseignants(MatiereRequest $request, int $id): JsonResponse
    {
        $enseignants = $request->input('enseignants', []);
        $res = $this->repository->assignerEnseignants($id, $enseignants);

        if (!$res) {
            return $this->returnError('Affectation impossible.');
        }

        return $this->returnSuccess('Affectation enregistrée avec succès', $this->repository->enseignants($id));
    }
}
