<?php

namespace Modules\Pedagogie\Http\Controllers;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Core\Http\Controllers\CoreController;
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
     * Liste élève sans pagination
     *
     * @return void
     */
    public function list() {
        return $this->repository->index();
    }

    /**
     * Liste des élèves
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return $this->repository->paginate();
    }


    /**
     * Création d'un élève
     *
     * @param EleveRequest $request
     * @return JsonResponse
     */
    public function store(EleveRequest $request): JsonResponse
    {
        $data = $request->validated();
        $eleve = $this->repository->store($data);
        if(!$eleve){
            return $this->returnError('Une erreur est survenue lors de la création d\'un élève');
        } else {
            return $this->returnSuccess('Elève créé avec succès', new EleveResource($eleve));
        }
    }


    /**
     * Afficher un élève
     *
     * @param [type] $code
     * @return EleveResource
     */
    public function show($code)
    {
        return $this->repository->show($code);
    }


    /**
     * Rechercher un élève
     *
     * @param [type] $keyword
     * @return AnonymousResourceCollection
     */
    public function search($keyword): AnonymousResourceCollection
    {
        return $this->repository->search($keyword);
    }


    /**
     * Mise à jour d'un élève
     *
     * @param EleveRequest $request
     * @param [type] $id
     * @return JsonResponse
     */
    public function update(EleveRequest $request, $id): JsonResponse
    {
        $data = $request->validated();
        $eleve = $this->repository->update($id, $data);
        if(!$eleve){
            return $this->returnError('Une erreur est survenue lors de la mise à jour de l\'élève');
        } else {
            return $this->returnSuccess('Elève mis à jour avec succès', new EleveResource($eleve));
        }
    }


    /**
     * Suppression d'un élève
     *
     * @param [type] $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $res = $this->repository->delete($id);
        if(!$res){
            return $this->returnError('Une erreur est survenue lors de la suppression de l\'élève');
        } else {
            return $this->returnSuccess('Elève supprimé avec succès');
        }
    }

}
