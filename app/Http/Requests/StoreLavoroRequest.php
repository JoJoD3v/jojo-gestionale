<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLavoroRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cliente_id' => ['required', 'exists:clienti,id'],
            'data_lavoro' => ['required', 'date'],
            'descrizione' => ['required', 'string'],
            'stato' => ['nullable', 'in:da_fare,in_corso,completato'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'cliente_id' => 'cliente',
            'data_lavoro' => 'data lavoro',
            'descrizione' => 'descrizione',
            'stato' => 'stato',
        ];
    }
}
