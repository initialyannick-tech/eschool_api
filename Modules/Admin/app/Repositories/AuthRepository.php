<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\User;
use Modules\Admin\Transformers\AuthResource;

class AuthRepository
{
    /**
     * Function de connexion
     *
     * @param [type] $data
     * @return array
     */
    public function login($data): array
    {
        $user = User::where('email', $data->email)->first();
        if($user) {

            if(password_verify($data->password, $user->password)) {

                if($user->status == User::INACTIVE) {
                    return array(
                        'success' => false,
                        'message' => 'Votre compte a été désactivé.'
                    );
                } else {
                    $token = $user->createToken('auth_token')->plainTextToken;
                    $data = array(
                        'success' => true,
                        'message' => 'Connexion réussie.',
                        'data' => array(
                            'user' => AuthResource::make($user),
                            'token' => $token,
                            'permissions' => $user->role->permissions->pluck('code')->toArray()
                        )
                    );
                    return $data;
                }
            } else {
                return array(
                    'success' => false,
                    'message' => 'Mot de passe incorrect.'
                );
            }
        } else {
            return array(
                'success' => false,
                'message' => 'Informations de connexion incorrectes.'
            );
        }
    }

    /**
     * Function de déconnexion
     *
     * @param [type] $request
     * @return void
     */
    public function logout($request)
    {
        return $request->user()->currentAccessToken()->delete();
    }
}
