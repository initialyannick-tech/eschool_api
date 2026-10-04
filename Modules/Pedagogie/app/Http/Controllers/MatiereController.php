<?php

namespace Modules\Pedagogie\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Pedagogie\Http\Requests\MatiereRequest;
use Modules\Pedagogie\Repositories\MatiereRepository;
use Modules\Pedagogie\Transformers\MatiereResource;

class MatiereController extends CoreController
{
    protected MatiereRepository $repository;

    public function __construct(MatiereRepository $repository)
    {
        $this->repository = $repository;
    }


    /**
     * Liste matière sans pagination
     *
     * @return void
     */
    public function list(): AnonymousResourceCollection
    {
        return $this->repository->index();
    }

    /**
     * Liste des matières
     *
     * @return AnonymousResourceCollection
     */
    public function paginate(): AnonymousResourceCollection
    {
        return $this->repository->paginate();
    }

    /**
     * Afficher une matière
     *
     * @param [type] $id
     * @return MatiereResource
     */
    public function show($id)
    {
        return $this->repository->show($id);
    }

    /**
     * Création d'une matière
     *
     * @param MatiereRequest $request
     * @return JsonResponse
     */
    public function store(MatiereRequest $request): JsonResponse
    {
        $data = $request->validated();
        $matiere = $this->repository->store($data);
        if (!$matiere) {
            return $this->returnError('Une erreur est survenue lors de la création de la matière.');
        }
        return $this->returnSuccess('Matière créée avec succès', $matiere);
    }

    /**
     * Mise à jour d'une matière
     *
     * @param MatiereRequest $request
     * @param [type] $id
     * @return JsonResponse
     */
    public function update(MatiereRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $matiere = $this->repository->update($data, $id);
        if (!$matiere) {
            return $this->returnError('Erreur lors de la mise à jour de la matière.');
        }
        return $this->returnSuccess('Matière mise à jour avec succès', $matiere);
    }

    /**
     * Suppression d'une matière
     *
     * @param [type] $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $matiere = $this->repository->destroy($id);
        if (!$matiere) {
            return $this->returnError('Impossible de supprimer cette matière.');
        }
        return $this->returnSuccess('Matière supprimée avec succès');
    }

    
    /**
     * Liste enseignants par matière
     *
     * @return void
     */
    public function enseignants(int $id): JsonResponse
    {
        $enseignants = $this->repository->enseignants($id);
        return $this->returnSuccess('Liste des enseignants de la matière', $enseignants);
    }

    
    /**
     * Assigner ensignant matière
     *
     * @return void
     */
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
