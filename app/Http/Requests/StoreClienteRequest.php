<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
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
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:clienti,email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'partita_iva' => ['nullable', 'string', 'max:50'],
            'note' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nome' => 'nome',
            'email' => 'email',
            'telefono' => 'telefono',
            'partita_iva' => 'partita IVA',
            'note' => 'note',
        ];
    }
}
