<?php

namespace Modules\Pedagogie\Repositories;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
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
        $eleves = Eleve::all();
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
        $eleve = new Eleve;
        $eleve->fill($data);
        if($eleve->save()){
            return $eleve;
        }
        return false;
    }

    /**
     * Récupérer tous les élèves
     *
     * @return AnonymousResourceCollection
     */
    public function paginate(): AnonymousResourceCollection
    {
        $eleves = Eleve::orderBy('id', 'desc')->paginate(10);
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
        $eleve = Eleve::where('matricule', $code)->with(['parents'])->first();
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
        $eleve = Eleve::find($id);
        $eleve->fill($data);
        if($eleve->save()){
            return $eleve;
        }
        return false;
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


}
