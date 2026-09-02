<?php

namespace Modules\Pedagogie\Http\Controllers;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Pedagogie\Http\Requests\AssignerParentRequest;
use Modules\Pedagogie\Http\Requests\EleveRequest;
use Modules\Pedagogie\Repositories\EleveRepository;
use Modules\Pedagogie\Transformers\EleveResource;


class EleveController extends CoreController
{
    protected EleveRepository $repository;

    public function __construct(EleveRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Liste complète élèves
     */
    public function list(): AnonymousResourceCollection
    {
        return $this->repository->index();
    }

    /**
     * Liste élèves paginée
     */
    public function paginate(): AnonymousResourceCollection
    {
        return $this->repository->paginate();
    }

    /**
     * Afficher un élève
     */
    public function show(int $id): EleveResource
    {
        return $this->repository->show($id);
    }

    /**
     * Créer un élève
     */
    public function store(EleveRequest $request): JsonResponse {
        $eleve = $this->repository->store($request->validated());
        return $this->returnSuccess('Élève créé avec succès.', new EleveResource($eleve->load('parent')));
    }

    /**
     * Modifier un élève
     */
    public function update(EleveRequest $request, int $id): JsonResponse {
        $eleve = $this->repository->update($request->validated(), $id);
        return $this->returnSuccess('Élève modifié avec succès.', new EleveResource($eleve));
    }

    /**
     * Supprimer un élève
     */
    public function destroy(int $id): JsonResponse {
        $this->repository->destroy($id);
        return $this->returnSuccess('Élève supprimé avec succès.');
    }

    /**
     * Assigner un élève à un parent
     */
    public function assignerParent(AssignerParentRequest $request, int $eleveId): JsonResponse {
        $eleve = $this->repository->assignerParent(
            $eleveId,
            $request->validated()[
                'parent_id'
            ]);
        return $this->returnSuccess('Élève assigné au parent avec succès.', new EleveResource($eleve));
    }

    /**
     * Retirer le parent d'un élève
     */
    public function retirerParent(int $eleveId): JsonResponse {
        $eleve = $this->repository->retirerParent($eleveId);
        return $this->returnSuccess('Parent retiré de l’élève avec succès.', new EleveResource($eleve));
    }

    /**
     * Liste des élèves d'un parent
     */
    public function byParent(int $parentId): AnonymousResourceCollection {
        return $this->repository->byParent($parentId);
    }
}
