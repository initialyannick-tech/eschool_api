<?php

namespace Modules\Pedagogie\Http\Controllers;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Pedagogie\Http\Requests\ClasseRequest;
use Modules\Pedagogie\Repositories\ClasseRepository;

class ClasseController extends CoreController
{
    protected ClasseRepository $repository;

    public function __construct(ClasseRepository $repository) {$this->repository = $repository;}

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

    /**
     * Afficher une classe.
     */
    public function show(int $id): JsonResponse
    {
        $res = $this->repository->show($id);
        if (!$res) {
            return $this->returnError('Classe introuvable');
        }
        return $this->returnSuccess('Classe récupérée avec succès', $res);
    }

    /**
     * Créer une classe.
     */
    public function store(ClasseRequest $request): JsonResponse {

        $data = $request->validated();
        $res = $this->repository->store($data);
        if (!$res) {
            return $this->returnError('Une classe portant ce nom existe déjà pour cette année scolaire.');
        }
        return $this->returnSuccess('Classe créée avec succès', $res);
    }

    /**
     * Modifier une classe.
     */
    public function update(ClasseRequest $request, int $id): JsonResponse {
        $data = $request->validated();
        $res = $this->repository->update($data, $id);
        if (!$res) {
            return $this->returnError('Erreur lors de la mise à jour de la classe.');
        }
        return $this->returnSuccess('Classe mise à jour avec succès', $res);
    }

    /**
     * Supprimer une classe.
     */
    public function destroy(int $id): JsonResponse {
        $res = $this->repository->destroy($id);
        if (!$res) {
            return $this->returnError('Impossible de supprimer cette classe.');
        }
        return $this->returnSuccess('Classe supprimée avec succès');
    }
}
