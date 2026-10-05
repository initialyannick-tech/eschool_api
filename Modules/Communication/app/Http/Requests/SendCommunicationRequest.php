<?php

namespace Modules\Communication\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendCommunicationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:5000'],
            'audience' => ['required', Rule::in(['all', 'role'])],
            'role_id' => [Rule::requiredIf($this->input('audience') === 'role'), 'nullable', 'integer', 'exists:roles,id'],
            'channels' => ['required', 'array', 'min:1'],
            'channels.*' => ['required', 'string', 'distinct', Rule::in(['database', 'mail'])],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role?->permissions()
            ->where('code', 'annonce.management')
            ->exists() ?? false;
    }
}
