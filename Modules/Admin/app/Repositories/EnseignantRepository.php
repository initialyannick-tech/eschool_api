<?php

namespace Modules\Admin\Repositories;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Admin\Models\User;
use Modules\Admin\Transformers\EnseignantResource;

class EnseignantRepository
{
    public function index(): AnonymousResourceCollection
    {
        $enseignants = User::where('role_id', User::ENSEIGNANT)
            ->where('status', User::ACTIVE)
            ->with(['specialite', 'matieres'])
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        return EnseignantResource::collection($enseignants);
    }

    public function paginate(): AnonymousResourceCollection
    {
        $enseignants = User::where('role_id', User::ENSEIGNANT)
            ->where('status', User::ACTIVE)
            ->with(['specialite', 'matieres'])
            ->orderBy('nom')
            ->orderBy('prenom')
            ->paginate(10);

        return EnseignantResource::collection($enseignants);
    }

    public function show(int $id)
    {
        return User::where('id', $id)
            ->where('role_id', User::ENSEIGNANT)
            ->with(['specialite', 'matieres'])
            ->firstOrFail();
    }

    public function store(array $data)
    {
        $data['role_id'] = User::ENSEIGNANT;
        $data['status'] = User::ACTIVE;
        $data['password'] = $data['password'] ?? 'azerty';
        $data['password_changed'] = $data['password_changed'] ?? User::ACTIVE;

        /** @var User $user */
        $user = new User();
        $user->fill($data);

        if ($user->save()) {
            return $user->fresh(['specialite', 'matieres']);
        }

        return false;
    }

    public function update(array $data, int $id)
    {
        /** @var User $user */
        $user = User::where('id', $id)
            ->where('role_id', User::ENSEIGNANT)
            ->firstOrFail();

        $user->fill($data);

        if ($user->save()) {
            return $user->fresh(['specialite', 'matieres']);
        }

        return false;
    }

    public function destroy(int $id): bool
    {
        /** @var User|null $user */
        $user = User::where('id', $id)
            ->where('role_id', User::ENSEIGNANT)
            ->first();

        if (!$user) {
            return false;
        }

        return (bool) $user->delete();
    }
}
