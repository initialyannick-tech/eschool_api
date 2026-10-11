<?php

namespace Modules\Pedagogie\Repositories;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Pedagogie\Models\ParentModel;
use Modules\Pedagogie\Transformers\ParentResource;

class ParentRepository
{
     /**
     * Liste des parents sans pagination
     *
     * @return void
     */
    public function index() {
        $parents = ParentModel::all();
        return ParentResource::collection($parents);
    }


    /**
     * Création d'un parent
     *
     * @param [type] $data
     * @return false|ParentModel
     */
    public function store($data)
    {
        $parents = new ParentModel;
        $parents->fill($data);
        if($parents->save()){
            return $parents;
        }
        return false;
    }

    /**
     * Récupérer tous les parents
     *
     * @return AnonymousResourceCollection
     */
    public function paginate(): AnonymousResourceCollection
    {
        $parents = ParentModel::orderBy('id', 'desc')->paginate(10);
        return ParentResource::collection($parents);
    }

    /**
     * Rechercher un parent par son nom, prénom
     *
     * @param [type] $keyword
     * @return AnonymousResourceCollection
     */
    public function search($keyword): AnonymousResourceCollection
    {
        $parents = ParentModel::where('nom', 'like', "%$keyword%")
                    ->orWhere('prenom', 'like', "%$keyword%")
                    ->paginate(10);
        return ParentResource::collection($parents);
    }


    /**
     * Récupérer un parent par son id
     *
     * @param [type] $id
     * @return ParentResource
     */
    public function show($code)
    {
        $parents = ParentModel::where('id', $code)->with(['eleves'])->first();
        return ParentResource::make($$parents);
    }


    /**
     * Mettre à jour un parent
     *
     * @param [type] $id
     * @param [type] $data
     * @return false
     */
    public function update($id, $data)
    {
        $parent = ParentModel::find($id);
        $parent->fill($data);
        if($parent->save()){
            return $parent;
        }
        return false;
    }

    /**
     * Supprimer un parent
     *
     * @param [type] $id
     * @return true
     */
    public function delete($id): bool
    {
        $parent = ParentModel::find($id);
        if($parent->delete()){
            return true;
        }
        return false;
    }
}
