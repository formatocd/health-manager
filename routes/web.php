<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ProfileController;
use App\Livewire\History\HealthStats;
use App\Livewire\History\HealthHistory;

Route::middleware(['auth', 'verified'])->group(function () {

    // 1. Dashboard será nuestro Calendario (Por ahora una vista simple, luego el componente)
    Route::view('/', 'dashboard')->name('dashboard');

    // 2. Historial (Placeholder por ahora)
    Route::view('/history', 'history')->name('history');

    // 3. Rutas de Perfil (Breeze estándar)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/private-attachment/{attachment}', [AttachmentController::class, 'show'])
    ->name('attachment.show');

    Route::get('/history', HealthHistory::class)->name('history');
    Route::get('/stats', HealthStats::class)->name('stats');

    Route::get('/export/history', [ExportController::class, 'downloadHistory'])->name('export.history');
    Route::get('/attachment/{id}', [FileController::class, 'show'])->name('attachment.show');
});

require __DIR__.'/auth.php';
