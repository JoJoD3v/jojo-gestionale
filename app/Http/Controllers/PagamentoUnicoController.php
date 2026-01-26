<?php

namespace App\Http\Controllers;

use App\Models\Pagamento;
use App\Models\Cliente;
use Illuminate\Http\Request;

class PagamentoUnicoController extends Controller
{
    /**
     * Display pagamenti unici
     */
    public function index(Request $request)
    {
        $query = Pagamento::with('cliente')
            ->where('cadenza', 'oneshot');

        // Filtro per stato
        if ($request->filled('stato')) {
            $query->where('stato', $request->stato);
        }

        // Filtro per cliente
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        // Filtro per range di date
        if ($request->filled('data_inizio')) {
            $query->where('data_scadenza', '>=', $request->data_inizio);
        }

        if ($request->filled('data_fine')) {
            $query->where('data_scadenza', '<=', $request->data_fine);
        }

        $pagamenti = $query->orderBy('data_scadenza', 'asc')->get();

        // Calcola totali e conteggi
        $totali = [
            'in_sospeso' => $pagamenti->where('stato', 'in_sospeso')->sum('importo'),
            'pagato' => $pagamenti->where('stato', 'pagato')->sum('importo'),
            'annullato' => $pagamenti->where('stato', 'annullato')->sum('importo'),
        ];

        $conteggi = [
            'in_sospeso' => $pagamenti->where('stato', 'in_sospeso')->count(),
            'pagato' => $pagamenti->where('stato', 'pagato')->count(),
            'annullato' => $pagamenti->where('stato', 'annullato')->count(),
        ];

        // Clienti per filtro
        $clienti = Cliente::orderBy('nome')->get();

        return view('pagamenti.unici.index', compact('pagamenti', 'totali', 'conteggi', 'clienti'));
    }
}
