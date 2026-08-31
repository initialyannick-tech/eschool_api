<?php

namespace Modules\Parametre\Repositories;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Modules\Parametre\Models\AnneeScolaire;
use Modules\Parametre\Transformers\AnneeScolaireResource;
use Throwable;

class AnneeScolaireRepository
{
    /**
     * Liste des années scolaires sans pagination.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $annees = AnneeScolaire::orderBy('id', 'desc')->get();
        return AnneeScolaireResource::collection($annees);
    }

    /**
     * Liste paginée des années scolaires.
     *
     * @return AnonymousResourceCollection
     */
    public function paginate(): AnonymousResourceCollection
    {
        $annees = AnneeScolaire::orderBy('id', 'desc')->paginate(10);
        return AnneeScolaireResource::collection($annees);
    }

    /**
     * Afficher une année scolaire.
     *
     * @param int $id
     * @return AnneeScolaire|null
     */
    public function show(int $id): ?AnneeScolaire
    {
        return AnneeScolaire::whereId($id)->first();
    }

    /**
     * Créer une année scolaire.
     *
     * @param array $data
     * @return AnneeScolaire|false
     */
    public function store(array $data): false|AnneeScolaire
    {
        $data['libelle'] = $data['annee_debut'] . '-' . $data['annee_fin'];
        $data['statut'] = 'preparee';
        $data['active'] = false;

        $annee = new AnneeScolaire();
        $annee->fill($data);

        if ($annee->save()) {
            return $annee;
        }
        return false;
    }

    /**
     * Mettre à jour une année scolaire.
     *
     * @param array $data
     * @param int $id
     * @return AnneeScolaire|false
     */
    public function update(array $data, int $id)
    {
        $annee = AnneeScolaire::whereId($id)->first();
        if (!$annee) {
            return false;
        }
        // Une année ouverte ou clôturée ne doit plus être modifiée.
        if ($annee->statut !== 'preparee') {
            return false;
        }
        $data['libelle'] = $data['annee_debut'] . '-' . $data['annee_fin'];
        $annee->fill($data);

        if ($annee->save()) {
            return $annee;
        }
        return false;
    }

    /**
     * Supprimer une année scolaire.
     *
     * Seules les années préparées peuvent être supprimées.
     *
     * @param int $id
     * @return bool
     */
    public function destroy(int $id): bool
    {
        $annee = AnneeScolaire::whereId($id)->first();
        if (!$annee) {
            return false;
        }
        if ($annee->statut !== 'preparee') {
            return false;
        }
        if ($annee->delete()) {
            return true;
        }
        return false;
    }

    /**
     * Ouvrir une année scolaire.
     *
     * L'année devient également l'année active.
     *
     * @param int $id
     * @return AnneeScolaire|false
     * @throws Throwable
     */
    public function ouvrir(int $id): false|AnneeScolaire
    {
        $annee = AnneeScolaire::whereId($id)->first();
        if (!$annee) {
            return false;
        }
        // Une année clôturée ne peut pas être rouverte.
        if ($annee->statut === 'cloturee') {
            return false;
        }

        DB::transaction(function () use ($annee) {
            // Désactiver toutes les autres années.
            AnneeScolaire::query()->update([
                'active' => false,
            ]);
            // Ouvrir et activer l'année.
            $annee->update([
                'statut' => 'ouverte',
                'active' => true,
                'date_ouverture' => now()->toDateString(),
                'date_cloture' => null,
            ]);
        });
        return $annee->fresh();
    }

    /**
     * Définir une année comme active.
     *
     * @param int $id
     * @return AnneeScolaire|false
     * @throws Throwable
     */
    public function activer(int $id)
    {
        $annee = AnneeScolaire::whereId($id)->first();
        if (!$annee) {
            return false;
        }
        // Seule une année ouverte peut être active.
        if ($annee->statut !== 'ouverte') {
            return false;
        }
        DB::transaction(function () use ($annee) {
            // Une seule année peut être active.
            AnneeScolaire::query()->update([
                'active' => false,
            ]);
            $annee->update([
                'active' => true,
            ]);
        });
        return $annee->fresh();
    }

    /**
     * Clôturer une année scolaire.
     *
     * @param int $id
     * @return AnneeScolaire|false
     * @throws Throwable
     */
    public function cloturer(int $id)
    {
        $annee = AnneeScolaire::whereId($id)->first();
        if (!$annee) {
            return false;
        }
        // Seule une année ouverte peut être clôturée.
        if ($annee->statut !== 'ouverte') {
            return false;
        }
        DB::transaction(function () use ($annee) {
            $annee->update([
                'statut' => 'cloturee',
                'active' => false,
                'date_cloture' => now()->toDateString(),
            ]);
        });
        return $annee->fresh();
    }

    /**
     * Récupérer l'année scolaire active.
     *
     * @return AnneeScolaireResource|null
     */
    public function active(): ?AnneeScolaireResource
    {
        $annee = AnneeScolaire::where('active', true)->first();
        if (!$annee) {
            return null;
        }
        return new AnneeScolaireResource($annee);
    }

    /**
     * Préparer automatiquement l'année scolaire suivante.
     *
     * Exemple :
     * 2026-2027 → 2027-2028
     *
     * @return AnneeScolaire|false
     */
    public function preparerSuivante(): false|AnneeScolaire
    {
        $anneeActive = AnneeScolaire::where('active', true)->first();
        if (!$anneeActive) {
            return false;
        }
        $anneeDebut = $anneeActive->annee_fin;
        $anneeFin = $anneeDebut + 1;
        $libelle = $anneeDebut . '-' . $anneeFin;
        $existante = AnneeScolaire::where('libelle', $libelle)->first();

        if ($existante) {
            return $existante;
        }
        $annee = new AnneeScolaire();
        $annee->fill([
            'libelle' => $libelle,
            'annee_debut' => $anneeDebut,
            'annee_fin' => $anneeFin,
            'statut' => 'preparee',
            'active' => false,
            'date_ouverture' => null,
            'date_cloture' => null,
            'description' => null,
        ]);
        if ($annee->save()) {
            return $annee;
        }
        return false;
    }
}
