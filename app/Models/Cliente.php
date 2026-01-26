<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use SoftDeletes;

    protected $table = 'clienti';

    protected $fillable = [
        'nome',
        'email',
        'telefono',
        'partita_iva',
        'note',
    ];

    /**
     * Relazione con i lavori del cliente
     */
    public function lavori(): HasMany
    {
        return $this->hasMany(Lavoro::class, 'cliente_id');
    }

    /**
     * Relazione con i pagamenti del cliente
     */
    public function pagamenti(): HasMany
    {
        return $this->hasMany(Pagamento::class, 'cliente_id');
    }
}
