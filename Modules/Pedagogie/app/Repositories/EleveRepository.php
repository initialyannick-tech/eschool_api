<?php

namespace Modules\Pedagogie\Repositories;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Admin\Models\Role;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\UserRepository;
use Modules\Pedagogie\Models\Eleve;
use Modules\Pedagogie\Transformers\EleveResource;

class EleveRepository
{

    /**
     * Liste des élève sans pagination
     *
     * @return void
     */
    public function index() {
        $eleves = Eleve::with(['parents', 'user'])->get();
        return EleveResource::collection($eleves);
    }


    /**
     * Création d'un élève
     *
     * @param [type] $data
     * @return false|Eleve
     */
    public function store($data)
    {
        return DB::transaction(function () use ($data) {
            $parentId = $data['parent_id'] ?? null;
            $pivotData = [
                'relation' => $data['relation'] ?? null,
                'responsable_principal' => $data['responsable_principal'] ?? false,
                'responsable_financier' => $data['responsable_financier'] ?? false,
            ];

            unset(
                $data['parent_id'],
                $data['relation'],
                $data['responsable_principal'],
                $data['responsable_financier'],
            );

            $eleve = new Eleve;
            $eleve->fill($data);
            $eleve->user_id = $this->resolveStudentAccount($data)?->id;
            $eleve->save();

            if ($parentId !== null) {
                $eleve->parents()->attach($parentId, $pivotData);
            }

            return $eleve->load(['parents', 'user']);
        });
    }

    /**
     * Récupérer tous les élèves
     *
     * @return AnonymousResourceCollection
     */
    public function paginate(): AnonymousResourceCollection
    {
        $eleves = Eleve::with(['parents', 'user'])->orderBy('id', 'desc')->paginate(10);
        return EleveResource::collection($eleves);
    }

    /**
     * Rechercher un élève par son nom, prénom ou code
     *
     * @param [type] $keyword
     * @return AnonymousResourceCollection
     */
    public function search($keyword): AnonymousResourceCollection
    {
        $eleves = Eleve::where('nom', 'like', "%$keyword%")
                    ->orWhere('prenom', 'like', "%$keyword%")
                    ->orWhere('matricule', 'like', "%$keyword%")
                    ->paginate(10);
        return EleveResource::collection($eleves);
    }


    /**
     * Récupérer un élève par son matricule
     *
     * @param [type] $id
     * @return EleveResource
     */
    public function show($code)
    {
        $eleve = Eleve::where('matricule', $code)->with(['parents', 'user'])->first();
        return EleveResource::make($eleve);
    }


    /**
     * Mettre à jour un élève
     *
     * @param [type] $id
     * @param [type] $data
     * @return false
     */
    public function update($id, $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $eleve = Eleve::findOrFail($id);
            $parentId = $data['parent_id'] ?? null;
            $hasParentLink = array_key_exists('parent_id', $data);
            $pivotData = [
                'relation' => $data['relation'] ?? null,
                'responsable_principal' => $data['responsable_principal'] ?? false,
                'responsable_financier' => $data['responsable_financier'] ?? false,
            ];
            unset(
                $data['parent_id'],
                $data['relation'],
                $data['responsable_principal'],
                $data['responsable_financier'],
            );

            $eleve->user_id = $this->resolveStudentAccount($data, $eleve)?->id;
            $eleve->fill($data);
            $eleve->save();

            if ($hasParentLink && $parentId !== null) {
                $eleve->parents()->syncWithoutDetaching([$parentId => $pivotData]);
            }

            return $eleve->fresh(['parents', 'user']);
        });
    }

    /**
     * Supprimer un élève
     *
     * @param [type] $id
     * @return true
     */
    public function delete($id): bool
    {
        $eleve = Eleve::find($id);
        if($eleve->delete()){
            return true;
        }
        return false;
    }

    public function resolveStudentAccount(array $data, ?Eleve $eleve = null): ?User
    {
        $email = isset($data['email']) ? trim($data['email']) : null;
        $userId = $data['user_id'] ?? null;
        $existingStudentUser = $eleve?->user;

        if (empty($userId) && ($email === null || $email === '') && ! $existingStudentUser) {
            return null;
        }

        $role = Role::query()->where('code', 'eleve')->first();
        if (! $role) {
            throw ValidationException::withMessages([
                'email' => 'Le rôle élève n’est pas configuré dans le système.',
            ]);
        }

        $user = null;
        if (! empty($userId)) {
            $user = User::query()->findOrFail($userId);
        } elseif ($email !== null && $email !== '') {
            $user = User::query()
                ->whereRaw('LOWER(TRIM(email)) = ?', [strtolower($email)])
                ->first();

            if (! $user && $existingStudentUser) {
                $user = $existingStudentUser;
                $user->email = $email;
            }

            if (! $user) {
                $user = app(UserRepository::class)->store([
                    'nom' => $data['nom'],
                    'prenom' => $data['prenom'],
                    'email' => $email,
                    'role_id' => $role->id,
                ]);
                if (! $user instanceof User) {
                    throw new \RuntimeException('La création du compte élève a échoué.');
                }
            }
        } elseif ($existingStudentUser) {
            if (array_key_exists('email', $data) && $data['email'] === null) {
                throw ValidationException::withMessages([
                    'email' => 'Une adresse e-mail est nécessaire pour conserver le compte de connexion.',
                ]);
            }

            $user = $existingStudentUser;
        }

        if (! $user instanceof User) {
            return null;
        }

        if ((int) $user->role_id !== (int) $role->id) {
            throw ValidationException::withMessages([
                'email' => 'Cette adresse e-mail appartient déjà à un compte qui n’a pas le rôle élève.',
            ]);
        }

        $linkedElsewhere = Eleve::query()
            ->where('user_id', $user->id)
            ->when($eleve, fn ($query) => $query->where('id', '!=', $eleve->id))
            ->exists();
        if ($linkedElsewhere) {
            throw ValidationException::withMessages([
                'email' => 'Ce compte est déjà lié à une autre fiche élève.',
            ]);
        }

        $user->fill([
            'nom' => $data['nom'] ?? $user->nom,
            'prenom' => $data['prenom'] ?? $user->prenom,
            'email' => $email ?? $user->email,
            'role_id' => $role->id,
        ]);
        $user->save();

        return $user;
    }

}
