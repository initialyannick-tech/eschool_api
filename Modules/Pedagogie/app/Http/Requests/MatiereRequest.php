<?php

namespace Modules\Pedagogie\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MatiereRequest extends FormRequest
{
    public function rules(): array
    {

        return [
            'libelle' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'actif' => ['nullable', 'boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

        public function messages(): array
    {
        return [
            'libelle.required' => 'Le libellé de la matière est obligatoire.',
            'libelle.string' => 'Le libellé de la matière doit être une chaîne de caractères.',
            'libelle.max' => 'Le libellé de la matière ne doit pas dépasser 255 caractères.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'actif.boolean' => 'Le statut actif doit être vrai ou faux.',
        ];
    }
}
