<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Lavoro;
use App\Models\Pagamento;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistiche
        $totaleClienti = Cliente::count();
        $lavoriMese = Lavoro::whereMonth('data_lavoro', Carbon::now()->month)
            ->whereYear('data_lavoro', Carbon::now()->year)
            ->count();
        $pagamentiInSospeso = Pagamento::where('stato', 'in_sospeso')->count();
        $importoInSospeso = Pagamento::where('stato', 'in_sospeso')->sum('importo');

        // Prossimi lavori (prossimi 7 giorni)
        $prossimiLavori = Lavoro::with('cliente')
            ->where('data_lavoro', '>=', Carbon::now())
            ->where('data_lavoro', '<=', Carbon::now()->addDays(7))
            ->orderBy('data_lavoro')
            ->limit(5)
            ->get();

        // Pagamenti in scadenza (prossimi 30 giorni)
        $pagamentiInScadenza = Pagamento::with('cliente')
            ->where('stato', 'in_sospeso')
            ->where('data_scadenza', '>=', Carbon::now())
            ->where('data_scadenza', '<=', Carbon::now()->addDays(30))
            ->orderBy('data_scadenza')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totaleClienti',
            'lavoriMese',
            'pagamentiInSospeso',
            'importoInSospeso',
            'prossimiLavori',
            'pagamentiInScadenza'
        ));
    }
}

