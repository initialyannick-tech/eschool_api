<?php

namespace Modules\Communication\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class ReplyToConversationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'body' => ['nullable', 'string', 'max:10000', 'required_without:attachment'],
            'attachment' => [
                'nullable',
                'file',
                File::types(['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'])->max('10mb'),
                'required_without:body',
            ],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role?->permissions()
            ->whereIn('code', ['message.envoyer', 'messagerie.management'])
            ->exists() ?? false;
    }
}
