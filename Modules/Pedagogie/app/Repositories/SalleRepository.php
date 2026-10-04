<?php

namespace Modules\Pedagogie\app\Repositories;

use Modules\Pedagogie\app\Models\Salle;
use Illuminate\Database\Eloquent\Collection;

class SalleRepository
{
    public function getAll(): Collection
    {
        return Salle::where('actif', true)->get();
    }

    public function findById(int $id): ?Salle
    {
        return Salle::find($id);
    }

    public function create(array $data): Salle
    {
        return Salle::create($data);
    }

    public function update(Salle $salle, array $data): bool
    {
        return $salle->update($data);
    }

    public function delete(Salle $salle): bool
    {
        return $salle->delete();
    }
}