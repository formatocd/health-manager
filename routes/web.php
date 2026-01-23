<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FileController;
// ❌ ELIMINADO: use App\Http\Controllers\ProfileController; (Ya no existe)
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Livewire\Admin\UserManagement;
use App\Livewire\History\HealthStats;
use App\Livewire\History\HealthHistory;

Route::middleware(['auth', 'verified'])->group(function () {

    // 1. Dashboard
    Route::view('/', 'dashboard')->name('dashboard');

    // 2. Historial (Componente Livewire)
    Route::get('/history', HealthHistory::class)->name('history');

    // 3. Estadísticas
    Route::get('/stats', HealthStats::class)->name('stats');

    // 4. Perfil (CORREGIDO: Usamos vista directa, Livewire se encarga del resto)
    Route::view('/profile', 'profile')->name('profile');

    // 5. Rutas de Archivos y Exportación
    Route::get('/private-attachment/{attachment}', [AttachmentController::class, 'show'])->name('attachment.show');
    Route::get('/export/history', [ExportController::class, 'downloadHistory'])->name('export.history');
    Route::get('/attachment/{id}', [FileController::class, 'show'])->name('attachment.show');
});

// Rutas de Administrador
Route::middleware(['auth', 'verified', EnsureUserIsAdmin::class])->group(function () {
    Route::get('/admin/users', UserManagement::class)->name('admin.users');
});

require __DIR__.'/auth.php';
