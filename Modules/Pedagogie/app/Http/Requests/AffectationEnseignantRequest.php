<?php

namespace Modules\Pedagogie\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AffectationEnseignantRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'matiere_ids' => ['nullable', 'array'],
            'matiere_ids.*' => ['integer', 'exists:matieres,id'],
            'enseignant_ids' => ['nullable', 'array'],
            'enseignant_ids.*' => ['integer', 'exists:users,id'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
