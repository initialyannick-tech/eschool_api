<?php

namespace Modules\Pedagogie\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignerParentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'parent_id' => ['required', 'integer', 'exists:parents,id',
            ],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'parent_id.required' => 'Le parent/tuteur est obligatoire.',
            'parent_id.integer' => 'Le parent/tuteur sélectionné est invalide.',
            'parent_id.exists' => 'Le parent/tuteur sélectionné n’existe pas.',
        ];
    }
}
