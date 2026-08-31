<?php

namespace Modules\Admin\Http\Controllers;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Admin\Http\Requests\AuthRequest;
use Modules\Admin\Repositories\AuthRepository;
use Modules\Core\Http\Controllers\CoreController;

class AuthController extends CoreController
{
    protected AuthRepository $repository;

    public function __construct(AuthRepository $repository)
    {
        $this->repository = $repository;
    }


    /**
     * Fonction connexion
     *
     * @unauthenticated
     *
     * @param AuthRequest $request
     * @return array
     */
    public function login(AuthRequest $request): array
    {
        $data = (object) $request->validated();
        return $this->repository->login($data);
    }

    /**
     * Fonction de déconnexion
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $logout = $this->repository->logout($request);
        if($logout) {
            return $this->returnSuccess('Déconnexion réussie.');
        } else {
            return $this->returnError('Une erreur est survenue lors de la déconnexion.');
        }
    }
}
