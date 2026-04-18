<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FileController;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Livewire\Admin\UserManagement;
use App\Livewire\History\HealthStats;
use App\Livewire\History\HealthHistory;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('/', 'dashboard')->name('dashboard');
    Route::get('/history', HealthHistory::class)->name('history');
    Route::get('/stats', HealthStats::class)->name('stats');
    Route::view('/profile', 'profile')->name('profile');
    Route::view('/settings', 'settings')->name('settings');
    Route::get('/private-attachment/{attachment}', [AttachmentController::class, 'show'])->name('private_attachment.show');
    Route::get('/export/history', [ExportController::class, 'downloadHistory'])->name('export.history');
    Route::get('/attachment/{id}', [FileController::class, 'show'])->name('attachment.show');
});

Route::middleware(['auth', 'verified', EnsureUserIsAdmin::class])->group(function () {
    Route::get('/admin/users', UserManagement::class)->name('admin.users');
});

require __DIR__.'/auth.php';
