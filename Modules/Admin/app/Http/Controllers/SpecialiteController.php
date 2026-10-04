<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Admin\Models\Specialite;
use Modules\Core\Http\Controllers\CoreController;

class SpecialiteController extends CoreController
{
    public function index(): JsonResponse
    {
        $specialites = Specialite::orderBy('nom')->get();

        return $this->returnSuccess('Liste des spécialités', $specialites);
    }
}
