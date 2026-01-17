<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;



Route::middleware(['auth', 'verified'])->group(function () {

    // 1. Dashboard será nuestro Calendario (Por ahora una vista simple, luego el componente)
    Route::view('/', 'dashboard')->name('dashboard');

    // 2. Historial (Placeholder por ahora)
    Route::view('/history', 'history')->name('history');

    // 3. Rutas de Perfil (Breeze estándar)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
