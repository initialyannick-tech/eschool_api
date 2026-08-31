<?php

namespace Modules\Admin\Http\Controllers;



use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Admin\Http\Requests\PasswordRequest;
use Modules\Admin\Http\Requests\UserRequest;
use Modules\Admin\Http\Requests\UserUpdateRequest;
use Modules\Admin\Repositories\UserRepository;
use Modules\Core\Http\Controllers\CoreController;

class UserController extends CoreController
{
    protected UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }


    /**
     * Liste utilisateur sans pagination
     *
     * @return AnonymousResourceCollection
     */
    public function list() {
        return $this->repository->index();
    }


    /**
     * Liste paginée des utilisateurs
     *
     * @return AnonymousResourceCollection
     */
    public function paginate(): AnonymousResourceCollection
    {
        return $this->repository->paginate();
    }


    /**
     * Affiche un utilisateur
     *
     * @param int $id
     * @return void
     */
    public function show($id)
    {
        return $this->repository->show($id);
    }

    /**
     * Créer un utilisateur
     *
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function store(UserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $res = $this->repository->store($data);
        if(!$res) {
            return $this->returnError('Erreur lors de la création de l\'utilisateur');
        } else {
            return $this->returnSuccess('Utilisateur créé avec succès', $res);
        }
    }


    /**
     * Mettre à jour un utilisateur
     *
     * @param UserUpdateRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UserUpdateRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $res = $this->repository->update($data, $id);
        if(!$res) {
            return $this->returnError('Erreur lors de la mise à jour de l\'utilisateur');
        } else {
            return $this->returnSuccess('Utilisateur mis à jour avec succès', $res);
        }
    }

    /**
     * Mise à jour du mot de passe par défaut
     *
     * @param PasswordRequest $request
     * @return JsonResponse
     */
    public function updatePassword(PasswordRequest $request): JsonResponse
    {
        $data = (object) $request->validated();
        $res = $this->repository->updatePassword($data);
        if(!$res) {
            return $this->returnError('Erreur lors de la mise à jour du mot de passe');
        } else {
            return $this->returnSuccess('Mot de passe mis à jour avec succès', $res);
        }
    }
}
