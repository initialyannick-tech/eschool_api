<?php

namespace Modules\Parametre\Http\Controllers;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Parametre\Http\Requests\AnneeScolaireRequest;
use Modules\Parametre\Models\AnneeScolaire;
use Modules\Parametre\Repositories\AnneeScolaireRepository;
use Throwable;

class AnneeScolaireController extends CoreController
{
    protected AnneeScolaireRepository $repository;

    public function __construct(AnneeScolaireRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Liste des années scolaires sans pagination.
     *
     * @return AnonymousResourceCollection
     */
    public function list(): AnonymousResourceCollection
    {
        return $this->repository->index();
    }

    /**
     * Liste paginée des années scolaires.
     *
     * @return AnonymousResourceCollection
     */
    public function paginate(): AnonymousResourceCollection
    {
        return $this->repository->paginate();
    }

    /**
     * Afficher une année scolaire.
     *
     * @param int $id
     * @return JsonResponse|AnneeScolaire
     */
    public function show(int $id)
    {
        $annee = $this->repository->show($id);
        if (!$annee) {
            return $this->returnError(
                'Année scolaire introuvable'
            );
        }
        return $annee;
    }

    /**
     * Créer une année scolaire.
     *
     * @param AnneeScolaireRequest $request
     * @return JsonResponse
     */
    public function store(AnneeScolaireRequest $request): JsonResponse
    {
        $data = $request->validated();

        $res = $this->repository->store($data);

        if (!$res) {
            return $this->returnError(
                'Erreur lors de la création de l\'année scolaire'
            );
        }

        return $this->returnSuccess(
            'Année scolaire créée avec succès',
            $res
        );
    }

    /**
     * Mettre à jour une année scolaire.
     *
     * @param AnneeScolaireRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(AnneeScolaireRequest $request, int $id): JsonResponse {
        $data = $request->validated();
        $res = $this->repository->update($data, $id);
        if (!$res) {
            return $this->returnError(
                'Erreur lors de la mise à jour de l\'année scolaire'
            );
        }
        return $this->returnSuccess(
            'Année scolaire mise à jour avec succès',
            $res
        );
    }

    /**
     * Supprimer une année scolaire.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $res = $this->repository->destroy($id);
        if (!$res) {
            return $this->returnError(
                'Impossible de supprimer cette année scolaire'
            );
        }
        return $this->returnSuccess(
            'Année scolaire supprimée avec succès'
        );
    }

    /**
     * Ouvrir une année scolaire.
     *
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function ouvrir(int $id): JsonResponse
    {
        $res = $this->repository->ouvrir($id);
        if (!$res) {
            return $this->returnError(
                'Impossible d\'ouvrir cette année scolaire'
            );
        }
        return $this->returnSuccess(
            'Année scolaire ouverte avec succès',
            $res
        );
    }

    /**
     * Activer une année scolaire.
     *
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function activer(int $id): JsonResponse
    {
        $res = $this->repository->activer($id);
        if (!$res) {
            return $this->returnError(
                'Impossible d\'activer cette année scolaire'
            );
        }
        return $this->returnSuccess(
            'Année scolaire définie comme active',
            $res
        );
    }

    /**
     * Clôturer une année scolaire.
     *
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function cloturer(int $id): JsonResponse
    {
        $res = $this->repository->cloturer($id);
        if (!$res) {
            return $this->returnError(
                'Impossible de clôturer cette année scolaire'
            );
        }
        return $this->returnSuccess(
            'Année scolaire clôturée avec succès',
            $res
        );
    }

    /**
     * Récupérer l'année scolaire active.
     *
     * @return JsonResponse
     */
    public function active(): JsonResponse
    {
        $res = $this->repository->active();
        if (!$res) {
            return $this->returnError(
                'Aucune année scolaire active'
            );
        }
        return $this->returnSuccess(
            'Année scolaire active récupérée avec succès',
            $res
        );
    }

    /**
     * Préparer automatiquement l'année scolaire suivante.
     *
     * @return JsonResponse
     */
    public function preparerSuivante(): JsonResponse
    {
        $res = $this->repository->preparerSuivante();
        if (!$res) {
            return $this->returnError(
                'Impossible de préparer la prochaine année scolaire'
            );
        }
        return $this->returnSuccess(
            'Prochaine année scolaire préparée avec succès',
            $res
        );
    }
}
