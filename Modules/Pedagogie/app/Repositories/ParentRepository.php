<?php

namespace Modules\Pedagogie\Repositories;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Pedagogie\Models\ParentModel;
use Modules\Pedagogie\Transformers\EleveResource;
use Modules\Pedagogie\Transformers\ParentResource;

class ParentRepository
{
    /**
     * Liste complète des parents/tuteurs
     */
    public function index(): AnonymousResourceCollection
    {
        $parents = ParentModel::with('eleves')->orderBy('nom')->orderBy('prenom')->get();
        return ParentResource::collection($parents);
    }

    /**
     * Liste paginée des parents/tuteurs
     */
    public function paginate(): AnonymousResourceCollection
    {
        $parents = ParentModel::with('eleves')->orderBy('nom')->orderBy('prenom')->paginate(15);
        return ParentResource::collection($parents);
    }

    /**
     * Afficher un parent/tuteur
     */
    public function show(int $id): ParentResource
    {
        $parent = ParentModel::with('eleves')->findOrFail($id);
        return new ParentResource($parent);
    }

    /**
     * Créer un parent/tuteur
     */
    public function store(array $data): ParentModel
    {
        return ParentModel::create($data);
    }

    /**
     * Modifier un parent/tuteur
     */
    public function update(array $data, int $id): ParentModel {
        $parent = ParentModel::findOrFail($id);
        $parent->update($data);
        return $parent->fresh(['eleves']);
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
}
