<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use SoftDeletes;

    protected $table = 'compiti_lavoro';

    protected $fillable = [
        'nome',
        'lavoro_id',
        'scadenza',
        'status',
    ];

    protected $casts = [
        'scadenza' => 'date',
    ];

    /**
     * Relazione con il lavoro
     */
    public function lavoro(): BelongsTo
    {
        return $this->belongsTo(Lavoro::class, 'lavoro_id');
    }

    /**
     * Verifica se il task è in ritardo
     */
    public function isInRitardo(): bool
    {
        return $this->status === 'in_sospeso' && $this->scadenza < now();
    }
}
