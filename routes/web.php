<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\LavoroController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\PagamentoUnicoController;
use App\Http\Controllers\PagamentoPeriodicoController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Routes protette da autenticazione
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Clienti
    Route::resource('clienti', ClienteController::class)->parameters(['clienti' => 'cliente']);

    // Lavori
    Route::resource('lavori', LavoroController::class)->parameters(['lavori' => 'lavoro']);

    // Pagamenti
    Route::resource('pagamenti', PagamentoController::class)->parameters(['pagamenti' => 'pagamento']);
    Route::post('pagamenti/{pagamento}/marca-pagato', [PagamentoController::class, 'marcaPagato'])
        ->name('pagamenti.marca-pagato');
    Route::post('pagamenti/{pagamento}/annulla', [PagamentoController::class, 'annulla'])
        ->name('pagamenti.annulla');
    
    // Pagamenti Unici
    Route::get('pagamenti-unici', [PagamentoUnicoController::class, 'index'])->name('pagamenti.unici.index');
    
    // Pagamenti Periodici
    Route::get('pagamenti-periodici', [PagamentoPeriodicoController::class, 'index'])->name('pagamenti.periodici.index');
    Route::post('pagamenti-periodici/{pagamento}/marca-ricorrenza-pagata', [PagamentoPeriodicoController::class, 'marcaRicorrenzaPagata'])
        ->name('pagamenti.periodici.marca-ricorrenza-pagata');
    Route::post('pagamenti-periodici/{pagamento}/annulla-ricorrenza', [PagamentoPeriodicoController::class, 'annullaRicorrenza'])
        ->name('pagamenti.periodici.annulla-ricorrenza');

    // Calendario
    Route::get('calendario', [CalendarioController::class, 'index'])->name('calendario.index');
    Route::get('calendario/eventi', [CalendarioController::class, 'getEventi'])->name('calendario.eventi');
    Route::get('calendario/dettagli-giorno', [CalendarioController::class, 'getDettagliGiorno'])
        ->name('calendario.dettagli-giorno');

    // Tasks
    Route::resource('tasks', TaskController::class)->parameters(['tasks' => 'task']);
    Route::post('tasks/{task}/completa', [TaskController::class, 'completa'])->name('tasks.completa');
});

require __DIR__.'/auth.php';

