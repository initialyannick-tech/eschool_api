<?php

namespace Modules\Pedagogie\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Modules\Pedagogie\Models\Cycle;
use Modules\Pedagogie\Models\Serie;

class ClasseRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'annee_scolaire_id' => ['required', 'integer', 'exists:annees_scolaires,id',],
            'cycle_id' => ['required', 'integer', 'exists:cycles,id',],
            'serie_id' => ['nullable', 'integer', 'exists:series,id',],
            'professeur_principal_id' => ['nullable', 'integer', 'exists:users,id',],
            'nom' => ['required', 'string', 'max:100',],
            'capacite' => ['required', 'integer', 'min:1', 'max:200',],
            'description' => ['nullable', 'string',],
            'actif' => ['nullable', 'boolean',],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {

            if (!$this->cycle_id) {
                return;
            }

            $cycle = Cycle::find($this->cycle_id);

            if (!$cycle) {
                return;
            }

            $nom = strtolower(trim($this->nom ?? ''));

            /*
             * Classes du premier cycle.
             */
            $premierCycle = [
                '6e',
                '6ème',
                '5e',
                '5ème',
                '4e',
                '4ème',
                '3e',
                '3ème',
            ];

            /*
             * Classes du second cycle.
             */
            $secondCycle = [
                '2nde',
                '2nd',
                '1ère',
                '1ere',
                'tle',
                'terminale',
            ];


            /*
             * =====================================================
             * VÉRIFICATION DU CYCLE
             * =====================================================
             */

            if (
                $cycle->code === 'premier'
                && !$this->startsWithAny($nom, $premierCycle)
            ) {
                $validator->errors()->add(
                    'nom',
                    'Cette classe ne correspond pas au premier cycle.'
                );
            }


            if (
                $cycle->code === 'second'
                && !$this->startsWithAny($nom, $secondCycle)
            ) {
                $validator->errors()->add(
                    'nom',
                    'Cette classe ne correspond pas au second cycle.'
                );
            }


            /*
             * =====================================================
             * PREMIER CYCLE
             * =====================================================
             *
             * Pour le premier cycle :
             *
             * - aucune série n'est obligatoire
             * - si une série est renseignée, elle doit être "Autre"
             *
             */

            if ($cycle->code === 'premier') {

                if ($this->serie_id) {

                    $serie = Serie::find($this->serie_id);

                    if (!$serie) {
                        $validator->errors()->add(
                            'serie_id',
                            'La série sélectionnée est invalide.'
                        );

                        return;
                    }

                    if (strtolower(trim($serie->libelle)) !== 'autre') {

                        $validator->errors()->add(
                            'serie_id',
                            'Pour une classe du premier cycle, seule la série "Autre" peut être renseignée.'
                        );
                    }
                }

                return;
            }


            /*
             * =====================================================
             * SECOND CYCLE
             * =====================================================
             *
             * La série est obligatoire pour :
             *
             * - 2nde
             * - 1ère
             * - Tle
             *
             */

            if ($cycle->code === 'second') {

                $serieObligatoire = $this->requiresSerie($nom);

                if ($serieObligatoire && !$this->serie_id) {

                    $validator->errors()->add(
                        'serie_id',
                        'La série est obligatoire pour les classes de 2nde, 1ère et Tle.'
                    );
                }
            }
        });
    }

    private function startsWithAny(string $value, array $values): bool {
        foreach ($values as $item) {
            if (str_starts_with($value, $item)) {
                return true;
            }
        }
        return false;
    }

    private function requiresSerie(string $nom): bool {
        return
            str_starts_with($nom, '2nde')
            || str_starts_with($nom, '2nd')
            || str_starts_with($nom, '1ère')
            || str_starts_with($nom, '1ere')
            || str_starts_with($nom, 'tle')
            || str_starts_with($nom, 'terminale');
    }

    public function messages(): array
    {
        return [
            'annee_scolaire_id.required' => 'L’année scolaire est obligatoire.',
            'annee_scolaire_id.exists' => 'L’année scolaire sélectionnée n’existe pas.',
            'cycle_id.required' => 'Le cycle est obligatoire.',
            'cycle_id.exists' => 'Le cycle sélectionné n’existe pas.',
            'serie_id.exists' => 'La série sélectionnée n’existe pas.',
            'professeur_principal_id.exists' => 'Le professeur principal sélectionné n’existe pas.',
            'nom.required' => 'Le nom de la classe est obligatoire.',
            'nom.string' => 'Le nom de la classe doit être une chaîne de caractères.',
            'nom.max' => 'Le nom de la classe ne peut pas dépasser 100 caractères.',
            'capacite.required' => 'La capacité de la classe est obligatoire.',
            'capacite.integer' => 'La capacité doit être un nombre entier.',
            'capacite.min' => 'La capacité doit être supérieure à zéro.',
            'capacite.max' => 'La capacité ne peut pas dépasser 200 élèves.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'actif.boolean' => 'Le champ actif doit être un booléen.',
        ];
    }
}
