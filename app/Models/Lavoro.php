<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Lavoro extends Model
{
    use SoftDeletes;

    protected $table = 'lavori';

    protected $fillable = [
        'cliente_id',
        'data_lavoro',
        'descrizione',
        'stato',
    ];

    protected $casts = [
        'data_lavoro' => 'date',
    ];

    /**
     * Relazione con il cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    /**
     * Scope per filtrare per cliente
     */
    public function scopePerCliente(Builder $query, int $clienteId): Builder
    {
        return $query->where('cliente_id', $clienteId);
    }

    /**
     * Scope per filtrare per stato
     */
    public function scopePerStato(Builder $query, string $stato): Builder
    {
        return $query->where('stato', $stato);
    }

    /**
     * Relazione con i task del lavoro
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'lavoro_id');
    }
}
