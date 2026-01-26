<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagamentoRicorrenza extends Model
{
    protected $table = 'ricorrenze_pagamenti';
    
    protected $fillable = [
        'pagamento_id',
        'data_ricorrenza',
        'stato',
        'data_pagamento',
        'note',
    ];

    protected $casts = [
        'data_ricorrenza' => 'date',
        'data_pagamento' => 'date',
    ];

    public function pagamento()
    {
        return $this->belongsTo(Pagamento::class);
    }
}
