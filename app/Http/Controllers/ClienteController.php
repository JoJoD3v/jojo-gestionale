<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cliente::query();

        // Ricerca
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $clienti = $query->orderBy('nome')->paginate(15);

        return view('clienti.index', compact('clienti'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clienti.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClienteRequest $request)
    {
        Cliente::create($request->validated());

        return redirect()->route('clienti.index')
            ->with('success', 'Cliente creato con successo!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        $cliente->load(['lavori', 'pagamenti']);
        
        return view('clienti.show', compact('cliente'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        return view('clienti.edit', compact('cliente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        $cliente->update($request->validated());

        return redirect()->route('clienti.index')
            ->with('success', 'Cliente aggiornato con successo!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clienti.index')
            ->with('success', 'Cliente eliminato con successo!');
    }
}

