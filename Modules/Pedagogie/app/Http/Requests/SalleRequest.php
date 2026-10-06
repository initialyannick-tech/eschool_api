<?php

namespace Modules\Pedagogie\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $salleId = $this->route('id');

        return [
            'code'     => 'required|string|max:50|unique:salles,code,' . $salleId,
            'nom'      => 'required|string|max:255',
            'capacite' => 'required|integer|min:1',
            'actif'    => 'nullable|boolean',
        ];
    }
}