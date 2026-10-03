<?php

namespace Modules\Pedagogie\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MatiereRequest extends FormRequest
{
    public function rules(): array
    {
        $matiereId = $this->route('id');

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('matieres', 'code')->ignore($matiereId)],
            'libelle' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'actif' => ['nullable', 'boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
