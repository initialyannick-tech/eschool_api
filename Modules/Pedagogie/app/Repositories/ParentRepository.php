<?php

namespace Modules\Pedagogie\Repositories;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Modules\Admin\Models\Role;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\UserRepository;
use Modules\Pedagogie\Models\Eleve;
use Modules\Pedagogie\Models\ParentModel;
use Modules\Pedagogie\Repositories\EleveRepository;
use Modules\Pedagogie\Transformers\EleveResource;
use Modules\Pedagogie\Transformers\ParentResource;

class ParentRepository
{
    /**
     * Liste complète des parents/tuteurs
     */
    public function index(): AnonymousResourceCollection
    {
        $parents = ParentModel::with(['eleves', 'user'])->orderBy('nom')->orderBy('prenom')->get();
        return ParentResource::collection($parents);
    }

    /**
     * Liste paginée des parents/tuteurs
     */
    public function paginate(): AnonymousResourceCollection
    {
        $parents = ParentModel::with(['eleves', 'user'])->orderBy('nom')->orderBy('prenom')->paginate(15);
        return ParentResource::collection($parents);
    }

    public function search(string $keyword): AnonymousResourceCollection
    {
        $search = '%'.trim($keyword).'%';
        $parents = ParentModel::query()
            ->with(['eleves', 'user'])
            ->where(function ($query) use ($search): void {
                $query->where('nom', 'like', $search)
                    ->orWhere('prenom', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhere('telephone', 'like', $search);
            })
            ->orderBy('nom')
            ->orderBy('prenom')
            ->paginate(15);

        return ParentResource::collection($parents);
    }

    /**
     * Afficher un parent/tuteur
     */
    public function show(int $id): ParentResource
    {
        $parent = ParentModel::with(['eleves', 'user'])->findOrFail($id);
        return new ParentResource($parent);
    }

    /**
     * Créer un parent/tuteur
     */
    public function store(array $data): ParentModel
    {
        return DB::transaction(fn () => $this->createParent($data));
    }

    public function storeDossier(array $data): array
    {
        return DB::transaction(function () use ($data): array {
            $parentData = $data['parent'];
            $pivotData = [
                'relation' => $parentData['relation'],
                'responsable_principal' => $parentData['responsable_principal'] ?? false,
                'responsable_financier' => $parentData['responsable_financier'] ?? false,
            ];
            unset(
                $parentData['relation'],
                $parentData['responsable_principal'],
                $parentData['responsable_financier'],
            );

            $parent = $this->createParent($parentData);
            $studentData = $data['eleve'];
            $eleve = new Eleve;
            $eleve->fill($studentData);
            $eleve->user_id = app(EleveRepository::class)->resolveStudentAccount($studentData)?->id;
            $eleve->save();
            $eleve->parents()->attach($parent->id, $pivotData);

            return [
                'parent' => $parent->load(['eleves', 'user']),
                'eleve' => $eleve->load('parents'),
            ];
        });
    }

    /**
     * Modifier un parent/tuteur
     */
    public function update(array $data, int $id): ParentModel {
        $parent = ParentModel::findOrFail($id);

        if (array_key_exists('relation', $data)
            || array_key_exists('responsable_principal', $data)
            || array_key_exists('responsable_financier', $data)) {
            throw ValidationException::withMessages([
                'relation' => 'La relation et les responsabilités doivent être modifiées pour un élève précis.',
            ]);
        }

        return DB::transaction(function () use ($data, $parent): ParentModel {
            $parent->user_id = $this->resolveParentAccount($data, $parent)?->id;
            $parent->fill($data);
            $parent->save();

            return $parent->fresh(['eleves', 'user']);
        });
    }

    /**
     * Supprimer un parent/tuteur
     */
    public function destroy(int $id): bool
    {
        $parent = ParentModel::findOrFail($id);
        return $parent->delete();
    }

    /**
     * Liste des enfants d'un parent
     */
    public function eleves(int $parentId): AnonymousResourceCollection {
        $parent = ParentModel::findOrFail($parentId);
        $eleves = $parent->eleves()->orderBy('nom')->orderBy('prenom')->get();
        return EleveResource::collection($eleves);
    }

    private function createParent(array $data): ParentModel
    {
        $user = $this->resolveParentAccount($data);
        $data['user_id'] = $user?->id;
        $data['statut'] ??= 'actif';

        return ParentModel::create($data);
    }

    private function resolveParentAccount(array $data, ?ParentModel $parent = null): ?User
    {
        $email = isset($data['email']) ? trim($data['email']) : null;
        $userId = $data['user_id'] ?? null;
        $existingParentUser = $parent?->user;

        if (empty($userId) && ($email === null || $email === '') && ! $existingParentUser) {
            return null;
        }

        $role = Role::query()->where('code', 'parent')->first();
        if (! $role) {
            throw ValidationException::withMessages([
                'email' => 'Le rôle parent n’est pas configuré dans le système.',
            ]);
        }

        $user = null;

        if (! empty($userId)) {
            $user = User::query()->findOrFail($userId);
        } elseif ($email !== null && $email !== '') {
            $user = User::query()
                ->whereRaw('LOWER(TRIM(email)) = ?', [strtolower($email)])
                ->first();

            if (! $user && $existingParentUser) {
                $user = $existingParentUser;
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
                    throw new RuntimeException('La création du compte parent a échoué.');
                }
            }
        } elseif ($existingParentUser) {
            if (array_key_exists('email', $data) && $data['email'] === null) {
                throw ValidationException::withMessages([
                    'email' => 'Une adresse e-mail est nécessaire pour conserver le compte de connexion.',
                ]);
            }

            $user = $existingParentUser;
        }

        if (! $user instanceof User) {
            return null;
        }

        if ((int) $user->role_id !== (int) $role->id) {
            throw ValidationException::withMessages([
                'email' => 'Cette adresse e-mail appartient déjà à un compte qui n’a pas le rôle parent.',
            ]);
        }

        $linkedElsewhere = ParentModel::query()
            ->where('user_id', $user->id)
            ->when($parent, fn ($query) => $query->where('id', '!=', $parent->id))
            ->exists();

        if ($linkedElsewhere) {
            throw ValidationException::withMessages([
                'email' => 'Ce compte est déjà lié à une autre fiche parent.',
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
