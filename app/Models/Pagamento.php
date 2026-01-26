<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Pagamento extends Model
{
    use SoftDeletes;

    protected $table = 'pagamenti';

    protected $fillable = [
        'cliente_id',
        'tipo_lavoro',
        'importo',
        'cadenza',
        'frequenza',
        'data_inizio',
        'data_scadenza',
        'stato',
    ];

    protected $casts = [
        'importo' => 'decimal:2',
        'data_inizio' => 'date',
        'data_scadenza' => 'date',
    ];

    /**
     * Relazione con il cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    /**
     * Scope per filtrare per stato
     */
    public function scopePerStato(Builder $query, string $stato): Builder
    {
        return $query->where('stato', $stato);
    }

    /**
     * Scope per filtrare per cliente
     */
    public function scopePerCliente(Builder $query, int $clienteId): Builder
    {
        return $query->where('cliente_id', $clienteId);
    }

    /**
     * Scope per pagamenti in scadenza
     */
    public function scopeInScadenza(Builder $query, Carbon $dataInizio, Carbon $dataFine): Builder
    {
        return $query->whereBetween('data_scadenza', [$dataInizio, $dataFine]);
    }

    /**
     * Genera le prossime scadenze per un pagamento periodico
     */
    public function generaScadenzePeriodiche(int $numeroScadenze = 12): array
    {
        if ($this->cadenza !== 'periodico' || !$this->frequenza) {
            return [];
        }

        $scadenze = [];
        $dataBase = $this->data_inizio ?? $this->data_scadenza;

        for ($i = 1; $i <= $numeroScadenze; $i++) {
            $nuovaData = match ($this->frequenza) {
                'mensile' => $dataBase->copy()->addMonths($i),
                'trimestrale' => $dataBase->copy()->addMonths($i * 3),
                'annuale' => $dataBase->copy()->addYears($i),
                default => null,
            };

            if ($nuovaData) {
                $scadenze[] = [
                    'cliente_id' => $this->cliente_id,
                    'tipo_lavoro' => $this->tipo_lavoro,
                    'importo' => $this->importo,
                    'cadenza' => 'periodico',
                    'frequenza' => $this->frequenza,
                    'data_inizio' => $this->data_inizio,
                    'data_scadenza' => $nuovaData,
                    'stato' => 'in_sospeso',
                ];
            }
        }

        return $scadenze;
    }

    /**
     * Marca il pagamento come pagato
     */
    public function marcaPagato(): bool
    {
        return $this->update(['stato' => 'pagato']);
    }

    /**
     * Annulla il pagamento
     */
    public function annulla(): bool
    {
        return $this->update(['stato' => 'annullato']);
    }
}
