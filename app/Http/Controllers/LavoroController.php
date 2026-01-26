<?php

namespace App\Http\Controllers;

use App\Models\Lavoro;
use App\Models\Cliente;
use App\Http\Requests\StoreLavoroRequest;
use App\Http\Requests\UpdateLavoroRequest;
use Illuminate\Http\Request;

class LavoroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Lavoro::with('cliente');

        // Filtro per cliente
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        // Filtro per data
        if ($request->filled('data_da')) {
            $query->where('data_lavoro', '>=', $request->data_da);
        }
        if ($request->filled('data_a')) {
            $query->where('data_lavoro', '<=', $request->data_a);
        }

        // Filtro per stato
        if ($request->filled('stato')) {
            $query->where('stato', $request->stato);
        }

        $lavori = $query->orderBy('data_lavoro', 'desc')->paginate(15);
        $clienti = Cliente::orderBy('nome')->get();

        return view('lavori.index', compact('lavori', 'clienti'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clienti = Cliente::orderBy('nome')->get();
        return view('lavori.create', compact('clienti'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLavoroRequest $request)
    {
        Lavoro::create($request->validated());

        return redirect()->route('lavori.index')
            ->with('success', 'Lavoro creato con successo!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Lavoro $lavoro)
    {
        $lavoro->load('cliente');
        return view('lavori.show', compact('lavoro'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lavoro $lavoro)
    {
        $clienti = Cliente::orderBy('nome')->get();
        return view('lavori.edit', compact('lavoro', 'clienti'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLavoroRequest $request, Lavoro $lavoro)
    {
        $lavoro->update($request->validated());

        return redirect()->route('lavori.index')
            ->with('success', 'Lavoro aggiornato con successo!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lavoro $lavoro)
    {
        $lavoro->delete();

        return redirect()->route('lavori.index')
            ->with('success', 'Lavoro eliminato con successo!');
    }
}

