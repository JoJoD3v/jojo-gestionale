<?php

namespace App\Http\Controllers;

use App\Models\Pagamento;
use App\Models\Cliente;
use App\Http\Requests\StorePagamentoRequest;
use App\Http\Requests\UpdatePagamentoRequest;
use Illuminate\Http\Request;

class PagamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pagamento::with('cliente');

        // Filtro per stato
        if ($request->filled('stato')) {
            $query->where('stato', $request->stato);
        }

        // Filtro per cliente
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        // Filtro per cadenza
        if ($request->filled('cadenza')) {
            $query->where('cadenza', $request->cadenza);
        }

        // Filtro per periodo
        if ($request->filled('data_da')) {
            $query->where('data_scadenza', '>=', $request->data_da);
        }
        if ($request->filled('data_a')) {
            $query->where('data_scadenza', '<=', $request->data_a);
        }

        $pagamenti = $query->orderBy('data_scadenza', 'desc')->paginate(15);

        // Calcola totali e conteggi
        $totali = [
            'in_sospeso' => Pagamento::where('stato', 'in_sospeso')->sum('importo'),
            'pagato' => Pagamento::where('stato', 'pagato')->sum('importo'),
            'annullato' => Pagamento::where('stato', 'annullato')->sum('importo'),
        ];

        $conteggi = [
            'in_sospeso' => Pagamento::where('stato', 'in_sospeso')->count(),
            'pagato' => Pagamento::where('stato', 'pagato')->count(),
            'annullato' => Pagamento::where('stato', 'annullato')->count(),
        ];

        return view('pagamenti.index', compact('pagamenti', 'totali', 'conteggi'));
    }

    /**
     * Display pagamenti unici (oneshot)
     */
    public function indexUnici(Request $request)
    {
        $query = Pagamento::with('cliente')->where('cadenza', 'oneshot');

        // Filtro per stato
        if ($request->filled('stato')) {
            $query->where('stato', $request->stato);
        }

        // Filtro per cliente
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        // Filtro per periodo
        if ($request->filled('data_da')) {
            $query->where('data_scadenza', '>=', $request->data_da);
        }
        if ($request->filled('data_a')) {
            $query->where('data_scadenza', '<=', $request->data_a);
        }

        $pagamenti = $query->orderBy('data_scadenza', 'desc')->paginate(15);

        // Calcola totali e conteggi solo per oneshot
        $totali = [
            'in_sospeso' => Pagamento::where('stato', 'in_sospeso')->where('cadenza', 'oneshot')->sum('importo'),
            'pagato' => Pagamento::where('stato', 'pagato')->where('cadenza', 'oneshot')->sum('importo'),
            'annullato' => Pagamento::where('stato', 'annullato')->where('cadenza', 'oneshot')->sum('importo'),
        ];

        $conteggi = [
            'in_sospeso' => Pagamento::where('stato', 'in_sospeso')->where('cadenza', 'oneshot')->count(),
            'pagato' => Pagamento::where('stato', 'pagato')->where('cadenza', 'oneshot')->count(),
            'annullato' => Pagamento::where('stato', 'annullato')->where('cadenza', 'oneshot')->count(),
        ];

        return view('pagamenti.unici.index', compact('pagamenti', 'totali', 'conteggi'));
    }

    /**
     * Display pagamenti periodici con navigazione mensile
     */
    public function indexPeriodici(Request $request)
    {
        // Ottieni il mese da visualizzare (default: mese corrente)
        $mese = $request->get('mese', now()->format('Y-m'));
        $dataInizio = \Carbon\Carbon::parse($mese . '-01')->startOfMonth();
        $dataFine = $dataInizio->copy()->endOfMonth();

        $query = Pagamento::with('cliente')
            ->whereIn('cadenza', ['mensile', 'trimestrale', 'semestrale'])
            ->whereBetween('data_scadenza', [$dataInizio, $dataFine]);

        // Filtro per stato
        if ($request->filled('stato')) {
            $query->where('stato', $request->stato);
        }

        // Filtro per cliente
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        // Filtro per cadenza
        if ($request->filled('cadenza')) {
            $query->where('cadenza', $request->cadenza);
        }

        $pagamenti = $query->orderBy('data_scadenza', 'asc')->get();

        // Calcola totali e conteggi per il mese corrente
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

        // Date per navigazione
        $mesePrecedente = $dataInizio->copy()->subMonth()->format('Y-m');
        $meseSuccessivo = $dataInizio->copy()->addMonth()->format('Y-m');
        $meseFormattato = $dataInizio->locale('it')->isoFormat('MMMM YYYY');

        return view('pagamenti.periodici.index', compact(
            'pagamenti', 
            'totali', 
            'conteggi', 
            'mese', 
            'mesePrecedente', 
            'meseSuccessivo',
            'meseFormattato'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clienti = Cliente::orderBy('nome')->get();
        return view('pagamenti.create', compact('clienti'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePagamentoRequest $request)
    {
        Pagamento::create($request->validated());

        return redirect()->route('pagamenti.index')
            ->with('success', 'Pagamento creato con successo!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pagamento $pagamento)
    {
        $pagamento->load('cliente');
        
        // Se è periodico, genera prossime scadenze
        $prossimaScadenze = [];
        if ($pagamento->cadenza === 'periodico') {
            $prossimaScadenze = $pagamento->generaScadenzePeriodiche(6);
        }
        
        return view('pagamenti.show', compact('pagamento', 'prossimaScadenze'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pagamento $pagamento)
    {
        $clienti = Cliente::orderBy('nome')->get();
        return view('pagamenti.edit', compact('pagamento', 'clienti'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePagamentoRequest $request, Pagamento $pagamento)
    {
        $pagamento->update($request->validated());

        return redirect()->route('pagamenti.index')
            ->with('success', 'Pagamento aggiornato con successo!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pagamento $pagamento)
    {
        $pagamento->delete();

        return redirect()->route('pagamenti.index')
            ->with('success', 'Pagamento eliminato con successo!');
    }

    /**
     * Marca il pagamento come pagato
     */
    public function marcaPagato(Pagamento $pagamento)
    {
        $pagamento->marcaPagato();

        return back()->with('success', 'Pagamento segnato come pagato!');
    }

    /**
     * Annulla il pagamento
     */
    public function annulla(Pagamento $pagamento)
    {
        $pagamento->annulla();

        return back()->with('success', 'Pagamento annullato!');
    }
}

