<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePagamentoRequest extends FormRequest
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
        $rules = [
            'cliente_id' => ['required', 'exists:clienti,id'],
            'tipo_lavoro' => ['required', 'string', 'max:255'],
            'importo' => ['required', 'numeric', 'min:0'],
            'cadenza' => ['required', 'in:oneshot,periodico'],
            'data_scadenza' => ['required', 'date'],
            'stato' => ['nullable', 'in:in_sospeso,pagato,annullato'],
        ];

        // Validazione condizionale per pagamenti periodici
        if ($this->input('cadenza') === 'periodico') {
            $rules['frequenza'] = ['required', 'in:mensile,trimestrale,annuale'];
            $rules['data_inizio'] = ['required', 'date'];
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'cliente_id' => 'cliente',
            'tipo_lavoro' => 'tipo di lavoro',
            'importo' => 'importo',
            'cadenza' => 'cadenza',
            'frequenza' => 'frequenza',
            'data_inizio' => 'data inizio',
            'data_scadenza' => 'data scadenza',
            'stato' => 'stato',
        ];
    }
}
